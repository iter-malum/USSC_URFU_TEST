import os, psycopg2, json
import flask
from flask import jsonify, render_template, Flask
from werkzeug.routing import BaseConverter
from flask_mongoengine import MongoEngine
import pymongo
import json
from bson import ObjectId
from flasgger import Swagger
from apispec import APISpec
from apispec.ext.marshmallow import MarshmallowPlugin
from apispec_webframeworks.flask import FlaskPlugin
from marshmallow import Schema, fields
from initmongodb import *
from initpostgresdb import *
from crypt import methods
from datetime import datetime, timedelta
import json
import jwt, hashlib, psycopg2, os
from flask import request, abort, jsonify
import logging

from flask import make_response, redirect, url_for
from flask import request
from flask import session

from flask_cors import CORS

import pickle
import base64

import secrets
import re


import UserManagement

logging.basicConfig(filename='/var/log/flask-app.log', level=logging.INFO, 
                    format='%(asctime)s %(levelname)s %(message)s')

class RegexConverter(BaseConverter):
    def __init__(self, url_map, *items):
        super(RegexConverter, self).__init__(url_map)
        self.regex = items[0]

app = Flask(__name__)
app.config['SEND_FILE_MAX_AGE_DEFAULT'] = 0
app.url_map.converters['regex'] = RegexConverter
app.secret_key = "super secret key" #required for session[]
#CORS(app, resources=r'/api/locations/*', origins='*', allow_headers='*', supports_credentials=True)

CORS(app, resources={r"/api/locations/*": {"origins": "*", "allow_headers": "*", "supports_credentials": True},
                      r"/api/products/*": {"origins": "*", "allow_headers": "*", "supports_credentials": False}})



spec = APISpec(
    title='CASP',
    version='1.0.10',
    openapi_version='2.0',
    plugins=[MarshmallowPlugin()]
)

swagger = Swagger(app)


def getConnection():
    conn = psycopg2.connect(host=os.environ['PG_DB_SERVER'],
                            database=os.environ['PG_DB_DB'],
                            user=os.environ['PG_DB_USERNAME'],
                            password=os.environ['PG_DB_PASSWORD'])
    return conn

@app.before_request
def log_request_info():
    app.logger.info(f'{request.remote_addr} - - [{datetime.now().strftime("%d/%b/%Y %H:%M:%S")}] "{request.method} {request.url} {request.environ.get("SERVER_PROTOCOL")}"')

@app.after_request
def log_response_info(response):
    app.logger.info(f'{request.remote_addr} - - [{datetime.now().strftime("%d/%b/%Y %H:%M:%S")}] "{request.method} {request.url} {request.environ.get("SERVER_PROTOCOL")}" {response.status_code} -')
    return response

class JSONEncoder(json.JSONEncoder):
    def default(self, o):
        if isinstance(o, ObjectId):
            return str(o)
        return json.JSONEncoder.default(self, o)

#login

JWT_SECRET = 'secret'
JWT_ALGORITHM = 'HS256'
JWT_EXP_DELTA_SECONDS = 180

accesslogslist = []

# GET method for healthcheck
@app.route('/api/healthcheck', methods=['GET'])
def get_healthcheck():
    return JSONEncoder().encode({'status': 'ok'})

# Routes with UI (Html response)
@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        email = request.form.get('email')
        password = request.form.get('password')
        encodedpassword = password.encode('utf-8')
        hashedpassword = hashlib.md5(encodedpassword)
        hashedhexdigestpassword = hashedpassword.hexdigest()
        app.logger.info(f'Login attempt - Email: {email}, Password: {password}')
        conn = getConnection()
        cur = conn.cursor()
        #cur.execute('SELECT * FROM users WHERE email=%(email)s',{"email":email})
        cur.execute("SELECT * FROM users WHERE email='%s' AND password='%s'" % (email, hashedhexdigestpassword))
        user = cur.fetchall()
        cur.close()
        conn.close()
        if (len(user) > 0):
            payload = {
                    'userid': user[0][0],
                    'name': user[0][1],
                    'email': user[0][2],
                    'role': user[0][4],
                    'exp': datetime.utcnow() + timedelta(minutes=JWT_EXP_DELTA_SECONDS),
                    'iat': datetime.utcnow(),
                    'nbf': datetime.utcnow()
                }
            jwt_token = jwt.encode(payload, JWT_SECRET, JWT_ALGORITHM)
            session["isloggedin"] = True
            session["user"] = email
            session["userid"] =  user[0][0]
            response = make_response(redirect(url_for('homepage')))
            response.set_cookie('access_token', jwt_token)
            return response
        else:
            return render_template('login.html', invalid = True)
    if request.method == 'GET':
        return render_template('login.html')

@app.route('/home', methods=['GET'])
def homepage():
    all_products = products.find({},{"_id":0})
    return render_template('home.html', all_products = all_products)

@app.route('/productadd', methods=['GET'])
def productadd():
    return render_template('products.html')

@app.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        username = request.form.get('name')
        email = request.form.get('email')
        password = hashlib.md5(request.form.get('password').encode('utf8')).hexdigest()
        role = request.form.get('role')
        secretkey = secrets.token_urlsafe(32)
        conn = getConnection()
        cur = conn.cursor()
        cur.execute('SELECT * FROM users;')
        if(role is not None):
            cur.execute('INSERT INTO users (name, email, role, password, key) VALUES (%s, %s, %s, %s, %s)',(username, email, role, password, secretkey))
        else:
            cur.execute('INSERT INTO users (name, email, role, password, key) VALUES (%s, %s, %s, %s, %s)',(username, email, 'user',password, secretkey))
        conn.commit()
        cur.close()
        conn.close()
        loginurl = url_for('login')
        response = make_response(render_template('register.html', registrationsuccess=True, login=loginurl))
        return response
    if request.method == 'GET':
        return render_template('register.html')

@app.route('/logout', methods=['GET'])
def logout():
    response = make_response(redirect(url_for('login')))
    response.set_cookie('access_token', '')
    session["isloggedin"] = False
    return response

@app.route('/coupons', methods=['GET'])
def display_coupons():
    access_token = request.cookies["access_token"]
    accesstokendecoded = jwt.decode(access_token, JWT_SECRET, JWT_ALGORITHM, options={"verify_signature": False, "verify_exp": False, "require": ["name", "email", "role", "exp", "iat", "iat"]})
    email = accesstokendecoded['email']
    coupon = coupons.find_one({"useremail": email},{"_id":0})
    if coupon:
        return render_template('coupons.html', couponcode = coupon["couponcode"])
    else:
        return render_template('coupons.html', couponcode = None)

@app.route('/mydevice', methods=['GET'])
def device():
    return render_template('mydevice.html')

@app.route('/findmydevice', methods=['GET'])
def finddevice():
    return render_template('findmydevice.html')

@app.route('/favorites', methods=['GET'])
def favorites():
    return render_template('favorites.html')

@app.route('/accesslogs/', methods=['GET'])
def accesslogs():
    accesslogslist.append(request.remote_addr + ' - ' + request.url)
    return render_template('accesslogs.html', accesslogslist = accesslogslist )

@app.route('/key', methods=['GET', 'POST'])  
def get_api_key():
    """Log in to get API key
    ---
    produces:
    - "application/json"
    responses:
      200:
        description: ""
      400:
        description: "Invalid ID supplied"
      404:
        description: "todo item not found"
    """
    if request.method == 'POST':
        email = request.form.get('email')
        password = hashlib.md5(request.form.get('password').encode('utf8')).hexdigest()
        print(password)
        conn = getConnection()
        cur = conn.cursor()
        cur.execute('SELECT * FROM users WHERE email=%(email)s',{"email":email})
        users = cur.fetchall()
        print(users)
        cur.close()
        conn.close()
        try:
            if(users[0][2] == email and users[0][3] == password):
                return render_template('documentation.html',apikey=users[0][5])
            else:
                return abort(403)
        except IndexError:
            return "user not available"
    if request.method == 'GET':
        return render_template('login.html')

#Reviews APIs

@app.route('/api/reviews/<regex("[0-9]+"):id>', methods=['GET'])
def list_reviews(id):
    """Get a review information
    ---
    tags:
        - reviews
    parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
          description: Review ID of the review
    produces:
    - "application/json"
    responses:
      200:
        description: "Request successful"
      400:
        description: "Invalid ID supplied"
      404:
        description: "review not found"
    """
    review = reviews.find_one({"id":int(id)},{"_id":0})
    return JSONEncoder().encode(review)

@app.route('/api/reviewsbyproduct/<regex("[0-9]+"):product_id>', methods=['GET'])
def list_reviews_by_product(product_id):
    """Get a review information by passing product id
    ---
    tags:
        - reviews
    parameters:
        - in: path
          name: product_id
          schema:
            type: integer
          required: true
          description: ID of a product
    produces:
    - "application/json"
    responses:
      200:
        description: "Request successful"
      400:
        description: "Invalid ID supplied"
      404:
        description: "review not found"
    """
    all_reviews = reviews.find({"product_id":int(product_id)},{"_id":0})
    return JSONEncoder().encode([reviews_1 for reviews_1 in all_reviews])

@app.route('/api/reviews/', methods=['GET'])
def all_reviews():
    """Get all review information
    ---
    tags:
        - reviews
    produces:
    - "application/json"
    responses:
      200:
        description: "Request successful"
      400:
        description: "Invalid ID supplied"
      404:
        description: "review not found"
    """
    all_reviews = reviews.find({},{"_id":0})
    return JSONEncoder().encode([reviews_1 for reviews_1 in all_reviews])

@app.route('/api/reviews/<regex("[0-9]+"):id>', methods=['PUT'])
def update_review(id):
    """Update a review
    ---
    tags:
        - reviews
    consumes:
        - "application/json; charset=utf-8"
    parameters:
        - name: id
          in: path
          schema:
            type: int
            required: true
            description: ID of the review
        - name: body
          in: body
          required: true
          schema:
            id : review
            required:
              - id
              - product_id
              - product_name
              - review
              - stars
            properties:
              id:
                type: integer
                description: ID of review as an integer
              product_id:
                type: integer
                description: ID of product as an integer
              product_name:
                type: string
                description: Name of the product
              review:
                type: string
                description: Review of the product
              stars:
                type: string
                description: A value between 1 and 5
    produces:
    - "application/json"
    responses:
      200:
        description: "Success"
      400:
        description: "Invalid ID supplied"
      404:
        description: "URL not found"
    """
    reviews.insert_one(request.json)
    review = reviews.update_one({"id":int(id)},{"$set": {"product_id":request.json["product_id"], "product_name":request.json["product_name"], "review":request.json["review"], "stars":request.json["stars"]}})
    review = reviews.find_one({"id":int(id)},{"_id":0})
    return JSONEncoder().encode(review)

@app.route('/api/reviews/', methods=['POST'])
def add_review():
    """Add a new review
    ---
    tags:
        - reviews
    consumes:
        - "application/json; charset=utf-8"
    parameters:
        - name: body
          in: body
          required: true
          schema:
            id : review
            required:
              - id
              - product_id
              - product_name
              - review
              - stars
            properties:
              id:
                type: integer
                description: ID of review as an integer
              product_id:
                type: integer
                description: ID of product as an integer
              product_name:
                type: string
                description: Name of the product
              review:
                type: string
                description: Review of the product
              stars:
                type: string
                description: A value between 1 and 5
    produces:
    - "application/json"
    responses:
      200:
        description: "Success"
      400:
        description: "Invalid ID supplied"
      404:
        description: "URL not found"
    """
    reviews.insert_one(request.json)
    review = reviews.find_one({"id":request.json["id"]},{"_id":0})
    return JSONEncoder().encode(review)

@app.route('/api/reviews/<regex("[0-9]+"):id>/', methods=['DELETE'])
def delete_review(id):
    """Delete a review
    ---
    tags:
        - reviews
    parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
          description: Review ID
    produces:
    - "application/json"
    responses:
      200:
        description: "Success"
      400:
        description: "Invalid ID supplied"
      404:
        description: "URL not found"
    """
    reviews.delete_one({"id":int(id)})
    return JSONEncoder().encode({"id":int(id)})

#Products APIs

@app.route('/api/products/<regex("[0-9]+"):id>', methods=['GET'])
def list_product(id):
    """Get a product information
    ---
    tags:
        - products
    parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
          description: Product ID of the product
    produces:
    - "application/json"
    responses:
      200:
        description: "Request successful"
      400:
        description: "Invalid ID supplied"
      404:
        description: "product not found"
    """
    product = products.find_one({"id":int(id)},{"_id":0})
    return JSONEncoder().encode(product)

@app.route('/api/products/', methods=['GET'])
def all_product():
    """Get all product information
    ---
    tags:
        - products
    produces:
    - "application/json"
    responses:
      200:
        description: "Request successful"
      400:
        description: "Invalid ID supplied"
      404:
        description: "product not found"
    """
    all_products = products.find({},{"_id":0})
    return JSONEncoder().encode([product_1 for product_1 in all_products])

@app.route('/api/products/<regex("[0-9]+"):id>', methods=['PUT'])
def update_product(id):
    """Update a new product
    ---
    tags:
        - products
    consumes:
        - "application/json; charset=utf-8"
    parameters:
        - name: id
          in: path
          schema:
            type: int
            required: true
            description: ID of the product
        - name: body
          in: body
          required: true
          schema:
            id : product
            required:
              - id
              - product_name
              - price
              - quantity
            properties:
              id:
                type: integer
                description: ID of product as an integer
              product_name:
                type: string
                description: Name of the product
              price:
                type: string
                description: Price of the product
              quantity:
                type: string
                description: Quantity of the product
    produces:
    - "application/json"
    responses:
      200:
        description: "Success"
      400:
        description: "Invalid ID supplied"
      404:
        description: "URL not found"
    """
    products.insert_one(request.json)
    product = products.update_one({"id":int(id)},{"$set": {"price":request.json["price"], "product_name":request.json["product_name"], "quantity":request.json["quantity"]}})
    product = products.find_one({"id":int(id)},{"_id":0})
    return JSONEncoder().encode(product)

@app.route('/api/products/', methods=['POST'])
def add_product():
    """Add a new product
    ---
    tags:
        - products
    consumes:
        - "application/json; charset=utf-8"
    parameters:
        - name: body
          in: body
          required: true
          schema:
            id : product
            required:
              - id
              - product_name
              - price
              - quantity
            properties:
              id:
                type: integer
                description: ID of product as an integer
              product_name:
                type: string
                description: Name of the product
              price:
                type: string
                description: Price of the product
              quantity:
                type: string
                description: Quantity of the product
    produces:
    - "application/json"
    responses:
      200:
        description: "Success"
      400:
        description: "Invalid ID supplied"
      404:
        description: "URL not found"
    """
    products.insert_one(request.json)
    product = products.find_one({"id":request.json["id"]},{"_id":0})
    return JSONEncoder().encode(product)

@app.route('/api/products/<regex("[0-9]+"):id>/', methods=['DELETE'])
def delete_product(id):
    """Delete a product
    ---
    tags:
        - products
    parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
          description: Product ID
    produces:
    - "application/json"
    responses:
      200:
        description: "Success"
      400:
        description: "Invalid ID supplied"
      404:
        description: "URL not found"
    """
    products.delete_one({"id":int(id)})
    return JSONEncoder().encode({"id":int(id)})

#Coupon APIs

@app.route('/api/coupons/validate/', methods=['POST'])
def validate_coupons():
    """Get a coupon information
    ---
    tags:
        - coupons
    consumes:
        - "application/json; charset=utf-8"
    parameters:
        - name: body
          in: body
          required: true
          schema:
            id : couponcode
            required:
              - couponcode
            properties:
              couponcode:
                type: string
                description: couponcode
    produces:
    - "application/json"
    responses:
      200:
        description: "Request successful"
      400:
        description: "Invalid ID supplied"
      404:
        description: "product not found"
    """
    coupon = coupons.find_one(request.json,{"_id":0})
    return JSONEncoder().encode(coupon)

# Swagger spec is not intentionally added for the below coupons endpoints, so they can be discovered through brute force
@app.route('/api/coupons/<regex("[0-9]+"):id>', methods=['GET'])
def list_coupons(id):
    coupon = coupons.find_one({"id":int(id)},{"_id":0})
    return JSONEncoder().encode(coupon)

@app.route('/api/coupons/', methods=['GET'])
def all_coupon():
    all_coupons = coupons.find({},{"_id":0})
    return JSONEncoder().encode([coupon_1 for coupon_1 in all_coupons])

@app.route('/api/coupons/<regex("[0-9]+"):id>', methods=['PUT'])
def update_coupon(id):
    coupons.insert_one(request.json)
    coupon = coupons.update_one({"id":int(id)},{"$set": {"couponcode":request.json["couponcode"], "validity":request.json["validity"], "useremail":request.json["useremail"]}})
    coupon = coupons.find_one({"id":int(id)},{"_id":0})
    return JSONEncoder().encode(coupon)

@app.route('/api/coupons/', methods=['POST'])
def add_coupon():
    coupons.insert_one(request.json)
    coupon = coupons.find_one({"id":request.json["id"]},{"_id":0})
    return JSONEncoder().encode(coupon)

@app.route('/api/coupons/<regex("[0-9]+"):id>/', methods=['DELETE'])
def delete_coupon(id):
    coupons.delete_one({"id":int(id)})
    return JSONEncoder().encode({"id":int(id)})


# Location APIs
@app.route('/api/locations/<regex("[0-9]+"):userid>', methods=['GET'])
def list_location(userid):
    location = locations.find_one({"userid":int(userid)},{"_id":0})
    return JSONEncoder().encode(location)

@app.route('/api/locations/byemail/<regex("[0-9]+"):useremail>', methods=['GET'])
def list_location_by_email(useremail):
    location = locations.find_one({"useremail":useremail},{"_id":0})
    return JSONEncoder().encode(location)

@app.route('/api/locations/', methods=['POST'])
def add_location():
    access_token = re.findall("^Bearer (.+)$", request.headers["Authorization"])[0]
    accesstokendecoded = jwt.decode(access_token, JWT_SECRET, JWT_ALGORITHM, options={"verify_signature": False, "verify_exp": False, "require": ["name", "email", "role", "exp", "iat", "iat"]})
    useremail = accesstokendecoded['email']
    userid = accesstokendecoded['userid']
    lat = request.json["lat"]
    long = request.json["long"]

    # if location exists for a user, update the location
    location = locations.find_one({"userid":int(userid)},{"_id":0})

    if location is None:
        location = {
            "lat": lat,
            "long": long,
            "useremail": useremail,
            "userid": userid
        }
        locations.insert_one(location)
    else:
        locations.update_one({"userid":int(userid)},{"$set": {"lat":lat, "long":long, "useremail":useremail}})

    location = locations.find_one({"userid":int(userid)},{"_id":0})
    return JSONEncoder().encode(location)


# User APIs
@app.route('/api/users/validate', methods=['POST'])
def validate_user_credentials():
    if request.method == 'POST':
        usercredentials = request.json
        useremail = usercredentials["email"]
        userpassword = usercredentials["password"]

        encodedpassword = userpassword.encode('utf-8')
        hashedpassword = hashlib.md5(encodedpassword)
        hashedhexdigestpassword = hashedpassword.hexdigest()
        conn = getConnection()
        cur = conn.cursor()
        #cur.execute('SELECT * FROM users WHERE email=%(email)s',{"email":useremail})
        cur.execute("SELECT * FROM users WHERE email='%s'" % useremail)
        # cur.execute("SELECT * FROM users WHERE email='%s' AND password='%s'" % (email, hashedhexdigestpassword))
        user = cur.fetchall()
        cur.close()
        conn.close()
        if (len(user) > 0):
            if(user[0][2] == useremail and user[0][3] == hashedhexdigestpassword):
                payload = {
                    'userid': user[0][0],
                    'name': user[0][1],
                    'email': user[0][2],
                    'role': user[0][4],
                    'exp': datetime.utcnow() + timedelta(minutes=JWT_EXP_DELTA_SECONDS),
                    'iat': datetime.utcnow(),
                    'nbf': datetime.utcnow()
                }
                return {"response" : "Success"}
            else:
                return {"response" : "Password incorrect"}
        else:
            return {"response" : "User not found"}

# User APIs
@app.route('/api/users/register', methods=['POST'])
def register_user():
    if request.method == 'POST':
        requestdata = usercredentials = request.json
        username = requestdata["name"]
        email = requestdata["email"]
        password = hashlib.md5(requestdata["password"].encode('utf8')).hexdigest()
        if "role" in requestdata:
            role = requestdata["role"]
        else:
            role = None
        secretkey = secrets.token_urlsafe(32)
        conn = getConnection()
        cur = conn.cursor()
        cur.execute('SELECT * FROM users;')
        if(role is not None):
            cur.execute('INSERT INTO users (name, email, role, password, key) VALUES (%s, %s, %s, %s, %s)',(username, email, role, password, secretkey))
        else:
            cur.execute('INSERT INTO users (name, email, role, password, key) VALUES (%s, %s, %s, %s, %s)',(username, email, 'user',password, secretkey))
        conn.commit()
        cur.close()
        conn.close()
        return {"response" : "Registered"}

# Favorites APIs
@app.route('/api/favorites/save', methods=['GET'])
def save_favorites():
    if request.method == 'GET':
        all_products = products.find({},{"product_name":1,"_id":0})
        favorite_products = JSONEncoder().encode([product_1 for product_1 in all_products])
        
        serializedbytes = pickle.dumps(favorite_products)
        serializedbytesbase64ed = base64.b64encode(serializedbytes)
        serializedbytesbase64edstring = serializedbytesbase64ed.decode()
        apiresponse = {"favorites": serializedbytesbase64edstring}
        return JSONEncoder().encode(apiresponse)
    
@app.route('/api/favorites/retrieve', methods=['POST'])
def retrieve_favorites():
    if request.method == 'POST':
        favoritesrequest = request.json
        favorites = favoritesrequest["favorites"]

        favoritesbase64 = None
        favoritesbase64 = favorites.encode()
        favoritesbytes = base64.b64decode(favoritesbase64)

        deserialized = pickle.loads(favoritesbytes)
        apiresponse = {"favorites": deserialized}
        return JSONEncoder().encode(apiresponse)

# User APIs v1
@app.route('/api/v1/users/<regex("[0-9]"):userId>')
def process_user(userId):
    """Get a particular user information
    ---
    tags:
        - version 1.0
    produces:
    - "application/json"
    responses:
      200:
        description: "Success"
      400:
        description: "Invalid ID supplied"
      404:
        description: "todo item not found"
    """  
    authorization = request.headers.get('Authorization')
    conn = getConnection()
    cur = conn.cursor()
    cur.execute(f'SELECT * FROM users where id={userId};')
    user = cur.fetchall()
    cur.close()
    conn.close()
    try:
        if(user[0][5] == authorization):
            userData = {}
            userData["name"] = user[0][1]
            userData["email"] = user[0][2]
            userData["role"] = user[0][4]
            userData["key"] = user[0][5]
        else:
            return flask.abort(403)
    except IndexError:
        userData = {'Error':'User not found'}
    return jsonify(userData)

# Get all user details
@app.route('/api/v1/users')
def allUsers():
    """Returns all users information
    ---
    tags:
        - version 1.0
    produces:
    - "application/json"
    responses:
      200:
        description: "users list"
      400:
        description: "Invalid ID supplied"
      404:
        description: "todo item not found"
    """
    authorization = request.headers.get('authorization')
    conn = getConnection()
    cur = conn.cursor()
    cur.execute('SELECT * FROM users;')
    user = cur.fetchall();
    print(user[0][5])
    print(authorization)
    if(user[0][5] == authorization):
        cur.execute('SELECT * FROM users;')
    else:
        return flask.abort(403)
    users = cur.fetchall()
    cur.close()
    conn.close()
    responseData = []
    try:
        for i in range(len(users)):
            data = {}
            data["id"] = users[i][0]
            data["name"] = users[i][1]
            data["email"] = users[i][2]
            data["role"] = users[i][4]
            responseData.append(data)
    except IndexError:
            responseData["Error"] = "User not found"
    return jsonify(responseData)

# Delete user [IDOR where low level user can delete admin]
@app.route('/api/v1/users/<regex("[0-9]"):userId>/delete', methods=["DELETE"])
def removeuser(userId):
    """Delete a particular user
    ---
    tags:
        - version 1.0
    produces:
    - "application/json"
    parameters:
        - in: path
          name: userId
          schema:
            type: int
          required: true
          description: user ID of the comment
    produces:
    - "application/json"
    responses:
      200:
        description: "User has been deleted"
      400:
        description: "Invalid ID supplied"
      404:
        description: "todo item not found"
    """
    conn = getConnection()
    cur = conn.cursor()
    cur.execute(f'DELETE FROM users WHERE id ={userId}')
    cur.execute(f'SELECT * FROM users;')
    user = cur.fetchall()
    conn.commit()
    cur.close()
    conn.close()
    return jsonify(Message="User has been deleted.")


#Sensitive information leakage - SQL Query leakage using Index Out of Range
@app.route('/api/v1/products/add', methods=["POST"])
def new_product():
    """Add new product
    ---
    tags:
        - version 1.0
    produces:
    - "application/json"
    responses:
      200:
        description: "list of tasks"
      400:
        description: "Invalid ID supplied"
      404:
        description: "todo item not found"
    """
    id = request.args.get('product_id')
    name = request.args.get('product_name')
    price = request.args.get('price')
    conn = getConnection()
    cur = conn.cursor()
    cur.execute('INSERT INTO products (product_name, price) VALUES (%s, %s, %s)',(id,name))
    cur.close()
    conn.close()
    return 'Product added2'

@app.route('/api/v1/products/<regex("[0-9]"):id>', methods=['GET'])
def get_product():
    """Get a product information
    ---
    tags:
        - version 1.0
    produces:
    - "application/json"
    responses:
      200:
        description: "success"
      400:
        description: "Invalid ID supplied"
      404:
        description: "todo item not found"
    """
    for i in dbcoll.find():
        print(i)
    items = dbcoll.find_one()
    return items

@app.after_request
def add_header(r):
    """
    Add headers to both force latest IE rendering engine or Chrome Frame,
    and also to cache the rendered page for 10 minutes.
    """
    r.headers["Cache-Control"] = "no-cache, no-store, must-revalidate"
    r.headers["Pragma"] = "no-cache"
    r.headers["Expires"] = "0"
    r.headers['Cache-Control'] = 'public, max-age=0'
    return r

@app.before_request
def check_authorization():
    redirecttologin, accesstokendecoded = obtainanddecodeaccesstoken("fromcookie")
    if ("login" in request.path) \
      or ("register" in request.path) \
        or ("flasgger_static" in request.path) \
          or ("apispec" in request.path) \
            or ("apidocs" in request.path) \
              or ("validate" in request.path) \
                or ("healthcheck" in request.path) \
                  or ("accesslogs" in request.path) \
                    or ("OPTIONS" in request.method):
        return
    elif "api/" in request.path:
        if '/reviews' in request.path:
            return
        redirecttologin, accesstokendecoded = obtainanddecodeaccesstoken("fromheader")
        if redirecttologin:
            return abort(403)
        role = accesstokendecoded['role']
        if 'user' in role:
            if ('/users' in request.path):
                return abort(403)
            elif ('/products' in request.path) and ('POST' in request.method):
                return abort(403)
            else:
                return
        elif 'admin' in role:
            return
        else:
            return abort(403)
    else:
        if redirecttologin:
            return render_template('login.html')

def obtainanddecodeaccesstoken(fromwhere):
    redirecttologin = False
    accesstokendecoded = ''
    access_token = ''
    try:
        if fromwhere == "fromcookie":
            access_token = request.cookies["access_token"]
        elif fromwhere =="fromheader":
            authorizationheader = request.headers["Authorization"]
            if authorizationheader.startswith("Bearer"):
                access_token = re.findall("^Bearer (.+)$", authorizationheader)[0]
    except:
        redirecttologin = True
    if access_token is None or access_token == '':
            redirecttologin = True
    else:
        try:
            accesstokendecoded = jwt.decode(access_token, JWT_SECRET, JWT_ALGORITHM, options={"verify_signature": False, "verify_exp": False, "require": ["name", "email", "role", "exp", "iat", "iat"]})
        except:
            redirecttologin = True
    return redirecttologin, accesstokendecoded
