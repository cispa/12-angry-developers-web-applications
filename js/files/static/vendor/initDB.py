import sqlite3


conn = sqlite3.connect('./db.sqlite3')

c = conn.cursor()

# Create table users
c.execute("""
    CREATE TABLE IF NOT EXISTS users (
         id SERIAL PRIMARY KEY,
         username VARCHAR(64),
         password TEXT
);""")

# Create table notes
c.execute("""
    CREATE TABLE IF NOT EXISTS notes (
         user VARCHAR(64),
         head VARCHAR(128),
         content TEXT,
         created TIMESTAMP
);""")

# Save (commit) the changes
conn.commit()

# We can also close the connection if we are done with it.
# Just be sure any changes have been committed or they will be lost.
conn.close()