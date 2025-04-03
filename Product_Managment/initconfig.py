import configparser
import os

def set_environment_variable(variablename, value):
    try:
        os.environ[variablename]
    except:
        os.environ[variablename] = value

config = configparser.ConfigParser()
config.read("config.ini")
APP = config['APP']
POSTGRESDB = config['POSTGRESDB']
MONGODB = config['MONGODB']

set_environment_variable("PG_DB_SERVER", POSTGRESDB["PG_DB_SERVER"])
set_environment_variable("PG_DB_USERNAME", POSTGRESDB["PG_DB_USERNAME"])
set_environment_variable("PG_DB_PASSWORD", POSTGRESDB["PG_DB_PASSWORD"])
set_environment_variable("PG_DB_DB", POSTGRESDB["PG_DB_DB"])

set_environment_variable("FLASK_APP", APP["FLASK_APP"])
set_environment_variable("FLASK_RUN_HOST", APP["FLASK_RUN_HOST"])

set_environment_variable("MONGO_SERVER", MONGODB["MONGO_SERVER"])
set_environment_variable("MONGO_DB", MONGODB["MONGO_DB"])
