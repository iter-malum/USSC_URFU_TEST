import os
import psycopg2

conn = psycopg2.connect(
        host=os.environ['PG_DB_SERVER'],
        database=os.environ['PG_DB_DB'],
        user=os.environ['PG_DB_USERNAME'],
        password=os.environ['PG_DB_PASSWORD'])

cur = conn.cursor()

#User1Password
#User2Password
#User3Password
#Admin1Password
#Admin2Password

#user queries
cur.execute('DROP TABLE IF EXISTS users;')
cur.execute('CREATE TABLE users (id serial PRIMARY KEY,'
                                 'name varchar (20) NOT NULL,'
                                 'email varchar (30) NOT NULL,'
                                 'password varchar (100) NOT NULL,'
                                 'role varchar (10) NOT NULL,'
                                 'key varchar (64) DEFAULT user NOT NULL,'
                                 'date_added date DEFAULT CURRENT_TIMESTAMP);'
                                 )

# echo -n password | md5
cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('BobUser1',
             'user1@ussc.ru',
             'a3aa81d99cb74b3fcf51915b35807b6b',
             'user',
             '5Jtg7P1djtYiCAIWX7ks5Oi5LrlY3ddYfTEMRAAgp9w')
            )

cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('TomUser2',
             'user2@ussc.ru',
             '860caa23c3f717177fe08413f17056d1',
             'user',
             '8OK3-M2H5UWyF60hiayz7ScGrcVfEEikEx97R0tGcxw')
            )

cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('AliceUser3',
             'user3@ussc.ru',
             '2349488ad7307333bff56a7d7795a5ec',
             'user',
             '8OK3-M2H5UWyF60hiayz7ScGrcVfEEikEx97R0tFlkd')
            )

cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('RogerAdmin1',
             'admin1@ussc.ru',
             '728dfd056e9c8b38e2341c5a648593fd',
             'admin',
             '8OK3-M2H5UWyF60hiayz7ScGrcVfEEikEx97R0tPasw')
            )

cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('LeeAdmin2',
             'admin2@ussc.ru',
             '7ba0fa51f59cf553496c19d1d173d07a',
             'admin',
             '8OK3-M2H5UWyF60hiayz7ScGrcVfEEikEx97R0tLsdw')
            )

cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('liam',
             'liam@ussc.ru',
             '1bbd886460827015e5d605ed44252251',
             'user',
             '8OK3-M2HIRWyF60hiayz7ScGrcVfEEikEx97R0tLsdw')
            )

cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('noah',
             'noah@ussc.ru',
             '5f4dcc3b5aa765d61d8327deb882cf99',
             'user',
             '8OK3-M2H5UWyF60hiayz7ScGrcSHEEikEx97R0tLsdw')
            )

cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('james',
             'james@ussc.ru',
             '25d55ad283aa400af464c76d713c07ad',
             'user',
             '8OK3-M2H5UWyF60hiayz7ScGrcVfFIikEx97R0tLsdw')
            )

cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('lucas',
             'lucas@ussc.ru',
             '22d7fe8c185003c98f97e5d6ced420c7',
             'user',
             '8OK3-M2H5UWyF60hiayz7ScGrcVfEEikEx97R0tMEdw')
            )

cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('henry',
             'henry@ussc.ru',
             '8ddcff3a80f4189ca1c9d4d902c3c909',
             'user',
             '8OK3-M2H5UWyF60hiayz7SSUrcVfEEikEx97R0tLsdw')
            )

cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('alex',
             'alex@ussc.ru',
             '2237f1e7a538a3c7da2cde2c707248c1',
             'user',
             '8OK3-M2H5UWyF60hifuz7ScGrcVfEEikEx97R0tLsdw')
            )

cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('olivia',
             'olivia@ussc.ru',
             'f35364bc808b079853de5a1e343e7159',
             'user',
             '8OK3-M2H5UWyFfehiayz7ScGrcVfEEikEx97R0tLsdw')
            )

cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('emma',
             'emma@ussc.ru',
             '5d93ceb70e2bf5daa84ec3d0cd2c731a',
             'user',
             '8OK3-M2H5UWyF60hiayz7ScGrc34EEikEx97R0tLsdw')
            )

cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('sophia',
             'sophia@ussc.ru',
             'f25a2fc72690b780b2a14e140ef6a9e0',
             'user',
             '8OK3-M2H5UWyF60hiayz7Sc98cVfEEikEx97R0tLsdw')
            )

cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('daphne',
             'daphne@ussc.ru',
             '8afa847f50a716e64932d995c8e7435a',
             'user',
             '8OK3-M2H5UWyF60hiayz7ScGr43fEEikEx97R0tLsdw')
            )

cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('mia',
             'mia@ussc.ru',
             '0571749e2ac330a7455809c6b0e7af90',
             'user',
             '8OK3-M2H5UWyF60hiayz7ScGrcVfEEikEx34R0tLsdw')
            )

cur.execute('INSERT INTO users (name, email, password, role, key)'
            'VALUES (%s, %s, %s, %s, %s)',
            ('evelyn',
             'evelyn@ussc.ru',
             '8f9b97bf3fad640ca17e9627e6bba1fd',
             'user',
             '8OK3-M2H5UWyF60hiayz7ScGrcVfEEikEx23R0tLsdw')
            )

# Product queries
cur.execute('DROP TABLE IF EXISTS products;')
cur.execute('CREATE TABLE products (product_id serial PRIMARY KEY,'
                                 'product_name varchar (20) NOT NULL,'
                                 'price INT NOT NULL,'
                                 'date_added date DEFAULT CURRENT_TIMESTAMP);'
                                 )

conn.commit()
cur.close()
conn.close()
