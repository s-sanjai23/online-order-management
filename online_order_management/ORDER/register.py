from flask import Flask, render_template, request, redirect, url_for, flash
import sqlite3

app = Flask(__name__)
app.secret_key = '8759f618f19e6112b5112d74e2d777cb46bfdeed91e200bf0d9d4e633b728466'  # Replace with a secure key

DATABASE = 'online_order_management.db'

def get_db():
    conn = sqlite3.connect(DATABASE)
    conn.row_factory = sqlite3.Row
    return conn

@app.route('/register', methods=['GET', 'POST'])
def register():
    message = ""
    if request.method == 'POST':
        username = request.form['username'].strip()
        email = request.form['email'].strip()
        password = request.form['password']
        role = request.form['role']

        hashed_password = password  # Replace with a hashing function like bcrypt or werkzeug.security.generate_password_hash

        try:
            conn = get_db()
            cursor = conn.cursor()
            cursor.execute(
                "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)",
                (username, email, hashed_password, role)
            )
            conn.commit()
            message = "Registration successful!"
        except sqlite3.Error as e:
            message = f"Error: {e}"
        finally:
            conn.close()

    return render_template('register.html', message=message)

if __name__ == '__main__':
    app.run(debug=True)