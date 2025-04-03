import os
import pymongo

myclient = pymongo.MongoClient("mongodb://" + os.environ['MONGO_SERVER'] + "/" + os.environ['MONGO_DB'])

db = myclient["casp"]
products = db["products"]
products.delete_many({})
productdata = [{"id": 1,"product_name":"Chocolates","price":"2","quantity":"5"},
{"id": 2,"product_name":"Biscuits","price":"5","quantity":"10"},
{"id": 3,"product_name":"Pickles","price":"3","quantity":"50"},
{"id": 4,"product_name":"Marmalade","price":"4","quantity":"20"}]
products.insert_many(productdata)

reviews = db["reviews"]
reviews.delete_many({})
reviewsdata = [{"id": 1, "product_id": 1, "product_name":"Chocolates","review":"Great Product","stars":"5"},
{"id": 2, "product_id": 1, "product_name":"Chocolates","review":"Stay away from this","stars":"1"},
{"id": 3, "product_id": 1, "product_name":"Chocolates","review":"Unless you like worms","stars":"1"},
{"id": 4, "product_id": 1, "product_name":"Chocolates","review":"okish","stars":"3"},
{"id": 5, "product_id": 1, "product_name":"Chocolates","review":"not bad","stars":"4"},
{"id": 6, "product_id": 2, "product_name":"Biscuits","review":"tasted unbaked","stars":"2"},
{"id": 7, "product_id": 2, "product_name":"Biscuits","review":"could be better","stars":"3"},
{"id": 8, "product_id": 2, "product_name":"Biscuits","review":"poor quality","stars":"1"},
{"id": 9, "product_id": 3, "product_name":"Pickles","review":"too much salt","stars":"2"},
{"id": 10, "product_id": 3, "product_name":"Pickles","review":"tasted bland","stars":"2"},
{"id": 11, "product_id": 3, "product_name":"Pickles","review":"Does it have MSG?","stars":"1"},
{"id": 12, "product_id": 3, "product_name":"Pickles","review":"I will not buy next time","stars":"3"},
{"id": 13, "product_id": 4, "product_name":"Marmalade","review":"I'd like more pulp","stars":"4"},
{"id": 14, "product_id": 4, "product_name":"Marmalade","review":"pulp size smaller","stars":"5"},
{"id": 15, "product_id": 4, "product_name":"Marmalade","review":"is there a no sugar variant?","stars":"5"},
{"id": 16, "product_id": 4, "product_name":"Marmalade","review":"good","stars":"4"}]
reviews.insert_many(reviewsdata)

coupons = db["coupons"]
coupons.delete_many({})
couponsdata = [{"id":"1371", "couponcode": "KFIE-PWOE-MJKD-ZOWK", "validity":"2y", "useremail":"user1@ussc.ru"},
{"id":"1372", "couponcode": "LKAS-UWIS-GFHG-NSWQ", "validity":"1y", "useremail":"user1@ussc.ru"},
{"id":"1373", "couponcode": "AHSG-YGYF-JSUY-XEBV", "validity":"3y", "useremail":"user1@ussc.ru"},
{"id":"1374", "couponcode": "POAH-MSHS-KSIW-CVDS", "validity":"2y", "useremail":"user1@ussc.ru"},
{"id":"1375", "couponcode": "HDJD-JDHF-IIRD-NJFH", "validity":"4y", "useremail":"user3@ussc.ru"},
{"id":"1376", "couponcode": "IJSS-MKDF-QWDR-LDKD", "validity":"1y", "useremail":"user3@ussc.ru"},
{"id":"1377", "couponcode": "JSUE-NKHS-LDKD-XBJD", "validity":"1y", "useremail":"user3@ussc.ru"}]
coupons.insert_many(couponsdata)

locations = db["locations"]
locations.delete_many({})
locationdata = [{"lat": "48.8584", "long":"2.2945", "useremail":"nouser@ussc.ru", "userid": 0},
{"lat": "51.5079", "long":"0.0877", "useremail":"user1@ussc.ru", "userid": 1},
{"lat": "29.9792", "long":"31.1342", "useremail":"user2@ussc.ru", "userid": 2},
{"lat": "32.5", "long":"44.4", "useremail":"user3@ussc.ru", "userid": 3},
{"lat": "41.8902", "long":"12.4922", "useremail":"admin1@ussc.ru", "userid": 4},
{"lat": "27.1751", "long":"78.0421", "useremail":"admin2@ussc.ru", "userid": 5}]
locations.insert_many(locationdata)
