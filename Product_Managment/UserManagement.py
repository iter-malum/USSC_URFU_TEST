from crypt import methods
from datetime import datetime, timedelta
import json
import jwt, hashlib, psycopg2, os
from flask import request, abort, jsonify
from app import app




def getConnection():
    conn = psycopg2.connect(host=os.environ['PG_DB_SERVER'],
                            database=os.environ['PG_DB_DB'],
                            user=os.environ['PG_DB_USERNAME'],
                            password=os.environ['PG_DB_PASSWORD'])
    return conn

