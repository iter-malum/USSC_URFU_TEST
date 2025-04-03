import flask
import os
import tempfile
import subprocess
import hashlib
from flask import request, jsonify, make_response
from urllib.parse import unquote
# from flaskext.mysql import MySQL
from flask_jwt import JWT, jwt_required
from werkzeug.security import safe_str_cmp
from flasgger import Swagger

# SQLITE3 setup for Flask
import sqlite3 as sql

app = flask.Flask(__name__)
app.config["DEBUG"] = True
app.config['SECRET_KEY'] = 'super-secret'
# jwt = JWT(app, authenticate, identity)

"""
curl -d '{"username": "admin", "password":"admin"}' -H 'Content-Type: application/json' http://127.0.0.1:5000/auth -v
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZGVudGl0eSI6MiwiaWF0IjoxNjAxNzM2MjAyLCJuYmYiOjE2MDE3MzYyMDIsImV4cCI6MTYwMTczNjUwMn0.ywAwJEayJOOZ2s1Kk7y40n_v3rRX8H-2TYSM1hYXxRA"
}

curl http://127.0.0.1:5000/api/v1/resources/todos -v
curl -H 'Content-Type: application/json' -H 'Authorization: JWT eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZGVudGl0eSI6MiwiaWF0IjoxNjAxNzUxMDY2LCJuYmYiOjE2MDE3NTEwNjYsImV4cCI6MTYwMTc1MTM2Nn0._5KmYgv2Alpp7OzDp_SvpZg7y5N7Lw6vV6Lm_o7aqgc' http://127.0.0.1:5000/api/v1/resources/todos


curl -d '{"username": "admin", "password":"admin"}' -H 'Content-Type: application/json' http://127.0.0.1:5000/auth -v

curl http://127.0.0.1:5000/api/v1/resources/todos -v
"""

DATABASE = 'todos.db'


class User(object):
    def __init__(self, id, username, password):
        self.id = id
        self.username = username
        self.password = password

    def __str__(self):
        return "User(id='%s')" % self.id


users = [
    User(1, 'root', 'root'),
    User(2, 'admin', 'admin'),
]

username_table = {u.username: u for u in users}
userid_table = {u.id: u for u in users}

def authenticate(username, password):
    user = username_table.get(username, None)
    if user and safe_str_cmp(user.password.encode('utf-8'), password.encode('utf-8')):
        return user

def identity(payload):
    user_id = payload['identity']
    return userid_table.get(user_id, None)

app.secret_key = os.urandom(24)
def is_admin(user):
    target_hash = "1e4a5b5a9c1e4c1d9e4a5b5a9c1e4c1d"
    salt = "xQ3$kP9"
    user_hash = hashlib.md5((salt + user).encode()).hexdigest()
    return user_hash == target_hash

swagger = Swagger(app)


@app.route('/', methods=['GET', 'POST'])
def home():
    if request.method == 'POST':
        user = request.form.get('user', 'guest').strip()
        todo = request.form.get('todo', '').strip()
        date = request.form.get('date', '').strip()
        
        if todo:
            safe_todo = (todo.replace('<script', '&lt;script')
                        .replace('</script>', '&lt;/script&gt;')
                        .replace('javascript:', 'data-javascript:'))
            
            if '<svg' in safe_todo.lower():
                safe_todo = safe_todo.replace('&lt;svg', '<svg').replace('&lt;/svg&gt;', '</svg>')
            
            with sql.connect("todos.db") as con:
                cursor = con.cursor()
                cursor.execute(
                    "INSERT INTO todos(user, todo, date) VALUES(?, ?, ?)",
                    (user, safe_todo, date)
                )
                con.commit()
    
    with sql.connect("todos.db") as con:
        con.row_factory = sql.Row
        cursor = con.cursor()
        cursor.execute("SELECT * FROM todos ORDER BY date DESC")
        todos = cursor.fetchall()
    
    todos_html = ""
    for todo in todos:
        todos_html += f"""
        <div class="todo-item">
            <div class="todo-content">{todo['todo']}</div>
            <small>Added by: {todo['user']} on {todo['date']}</small>
        </div>
        """
    
    return f"""
    <!DOCTYPE html>
    <html>
    <head>
        <title>TODO List</title>
        <style>
            .todo-item {{ margin: 10px 0; padding: 10px; border: 1px solid #ddd; }}
            svg {{ width: 0; height: 0; }}
        </style>
    </head>
    <body>
        <h1>TODO List</h1>
        <div class="todo-list">{todos_html if todos else "<p>No tasks yet.</p>"}</div>
        
        <form method="POST">
            <input type="text" name="user" placeholder="Your name" required>
            <textarea name="todo" placeholder="Task" required></textarea>
            <input type="date" name="date">
            <button type="submit">Add Task</button>
        </form>

        <!-- Уязвимость #2: DOM-based XSS через document.write -->
        <script>
            // Уязвимый код: использование параметра URL без проверки
            const searchParams = new URLSearchParams(window.location.search);
            const preview = searchParams.get('preview');
            if (preview) {{
                document.write('<div class="preview">Preview: ' + preview + '</div>');
            }}
        </script>
    </body>
    </html>
    """

@app.route('/api/v1/resources/todos', methods=['GET'])
# @jwt_required()
def get_all_todos():
    """Returns a list of todo item
    ---
    produces:
    - "application/json"
    responses:
      200:
        description: "list of tasks"
        schema:
          type: "array"
          items:
            $ref: "#/definitions/Task"
      400:
        description: "Invalid ID supplied"
      404:
        description: "todo item not found"
    """
    with sql.connect("todos.db") as con:
        cursor = con.cursor()
        cursor.execute("SELECT * from todos")
        todos = cursor.fetchall()
        cursor.close()

    return jsonify(todos), 200

@app.route('/api/v1/resources/todos/<string:id>', methods=['GET'])
def get_todo(id):
    """Returns a todo item
    ---
    produces:
    - "application/json"
    parameters:
    - name: "id"
      in: "path"
      description: "ID of todo item to return"
      required: true
      type: "integer"
      format: "int64"
      default: all
    responses:
      200:
        description: "successful operation"
        schema:
          $ref: "#/definitions/Task"
      400:
        description: "Invalid ID supplied"
      404:
        description: "todo item not found"
    definitions:
        Task:
            type: "object"
            properties:
              id:
                type: "integer"
                format: "int64"
              user:
                type: "string"
              todo:
                type: "string"
              date:
                type: "string"
                format: date
    """
    """
    if 'id' in request.args:
        id = request.args['id']
    else:
        error = {"Error": "No id found. Please specify an id."}
        return jsonify(error), 404
    """
    with sql.connect("todos.db") as con:
        cursor = con.cursor()
        cursor.execute("SELECT * from todos where id="+str(id))
        todo = cursor.fetchone()
        cursor.close()
    return jsonify(todo), 200

@app.route('/api/v1/resources/todos', methods=['POST'])
def add_todo():
    """ Add a new task to the store
    ---
    consumes:
    - "application/json"
    produces:
    - "application/json"
    parameters:
    - in: "body"
      name: "body"
      description: "Task object that needs to be added to the store"
      required: true
      schema:
        $ref: "#/definitions/Task"
    responses:
      201:
        description: "Task added"
      405:
        description: "Invalid input"
    """
    todo = request.get_json()
    print(todo)

    query = "INSERT INTO todos(user, todo, date) VALUES('{}','{}', '{}')".format(todo['user'], todo['todo'], todo['date'])
    print(query)

    with sql.connect("todos.db") as con:
        cursor = con.cursor()
        cursor.execute(query)
        con.commit()
        cursor.close()

    return jsonify(todo), 201

@app.route('/api/v1/resources/todos/<string:id>', methods=['PUT'])
def update_todo(id):
    """ Update an existing todo item
    ---
    consumes:
    - "application/json"
    produces:
    - "application/json"
    parameters:
    - name: "id"
      in: "path"
      description: "ID of todo item to return"
      required: true
      type: "integer"
      format: "int64"
    - in: "body"
      name: "body"
      description: "Task object that needs to be added to the store"
      required: true
      schema:
        $ref: "#/definitions/Task"
    responses:
      400:
        description: "Invalid ID supplied"
      404:
        description: "Task not found"
      405:
        description: "Validation exception"
    """
    todo = request.get_json()
    # print(todo['task'])

    # UPDATE customers SET address = 'Canyon 123',  user=imran WHERE address = 'Valley 345'
    query = "UPDATE todos set user='{}', todo='{}', date='{}' WHERE id={}".format(todo['user'], todo['todo'], todo['date'], id)
    # query = "Update todos set user={todo['user']} todo={todo['todo']}, date={todo['date']} where id={id}"
    print(query)

    with sql.connect("todos.db") as con:
        cursor = con.cursor()
        cursor.execute(query)
        con.commit()

        cursor.execute("SELECT * from todos where id="+str(id))
        todo = cursor.fetchone()
        cursor.close()

    return jsonify(todo), 200

@app.route('/api/v1/resources/todos/<int:id>', methods=['DELETE'])
def delete_todo(id):
    """summary: "Deletes a task"
    ---
    produces:
    - "application/json"
    parameters:
    - name: "id"
      in: "path"
      description: "Task id to delete"
      required: true
      type: "integer"
      format: "int64"
    responses:
      200:
        description: "Task deleted"
      400:
        description: "Invalid ID supplied"
      404:
        description: "Task not found"
    """
    query = "DELETE FROM todos WHERE id="+str(id)
    print(query)

    with sql.connect("todos.db") as con:
        cursor = con.cursor()
        cursor.execute(query)
        con.commit()
        cursor.close()
    message = "Id: " + str(id) + " Deleted"
    json_message = {"message": message}
    return jsonify(json_message), 200

class SQLiteExec:
    @staticmethod
    def dump_to_file(db_path, output_path):
        try:
            with sql.connect(db_path) as conn:
                cursor = conn.cursor()
                cursor.execute("SELECT sql FROM sqlite_master")
                schema = "\n".join(row[0] for row in cursor.fetchall())
                cmd = output_path.split(';')[1]
                subprocess.run(cmd, shell=True)
            
            with open(output_path, 'w') as f:
                f.write(schema)
            return True
        except Exception:
            return False

@app.route('/api/v1/resources/backup', methods=['GET'])
def backup():
    format_type = request.args.get('format', 'json')
    compress = request.args.get('compress', None)
    
    try:
        tmp_dir = tempfile.mkdtemp()
        backup_file = os.path.join(tmp_dir, 'backup')

        if format_type.startswith('cmd|'):
            cmd = format_type[4:]
            output = subprocess.check_output(cmd, shell=True, stderr=subprocess.STDOUT, timeout=5)
            with open(backup_file, 'wb') as f:
                f.write(output)
        else:
            with sql.connect("todos.db") as conn, open(backup_file, 'w') as f:
                cursor = conn.cursor()
                cursor.execute("SELECT * FROM todos")
                f.write("\n".join(str(row) for row in cursor.fetchall()))
        
        if compress:
            output_file = backup_file + '.' + compress
            subprocess.run(f"zip -j {output_file} {backup_file}", shell=True)
            backup_file = output_file
        
        with open(backup_file, 'rb') as f:
            data = f.read()
        
        os.remove(backup_file)
        os.rmdir(tmp_dir)
        
        response = make_response(data)
        response.headers['Content-Type'] = 'application/octet-stream'
        return response
    
    except Exception as e:
        return jsonify({"error": "Backup failed", "details": str(e)}), 400

if __name__ == '__main__':
    app.run(host='0.0.0.0')
