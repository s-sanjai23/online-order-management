from flask import Flask, render_template, request, redirect, url_for, session, flash
import sqlite3
from werkzeug.security import generate_password_hash, check_password_hash

app = Flask(__name__)
app.secret_key = 'your_secret_key'  # Replace with a secure key
DATABASE = 'online_order_management.db'


def get_db():
    conn = sqlite3.connect(DATABASE)
    conn.row_factory = sqlite3.Row
    return conn


@app.route('/')
def index():
    return render_template('index.html')


@app.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        username = request.form['username'].strip()
        email = request.form['email'].strip()
        password = generate_password_hash(request.form['password'])
        role = request.form['role']

        try:
            conn = get_db()
            cursor = conn.cursor()
            cursor.execute(
                "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)",
                (username, email, password, role)
            )
            conn.commit()
            flash("Registration successful!", "success")
            return redirect(url_for('login'))
        except sqlite3.IntegrityError:
            flash("Username or email already exists!", "danger")
        finally:
            conn.close()

    return render_template('register.html')


@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form['username'].strip()
        password = request.form['password']

        conn = get_db()
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM users WHERE username = ?", (username,))
        user = cursor.fetchone()
        conn.close()

        if user and check_password_hash(user['password'], password):
            session['username'] = user['username']
            session['role'] = user['role']
            session['user_id'] = user['user_id']
            if user['role'] == 'admin':
                return redirect(url_for('dashboard'))
            else:
                return redirect(url_for('home'))
        else:
            flash("Invalid username or password!", "danger")

    return render_template('login.html')


@app.route('/dashboard', methods=['GET', 'POST'])
def dashboard():
    if 'username' not in session or session['role'] != 'admin':
        return redirect(url_for('login'))

    conn = get_db()
    cursor = conn.cursor()

    if request.method == 'POST' and 'update_order' in request.form:
        order_id = request.form['order_id']
        new_status = request.form['status']
        cursor.execute("UPDATE orders SET status = ? WHERE order_id = ?", (new_status, order_id))
        conn.commit()
        flash("Order updated successfully!", "success")

    orders = cursor.execute("SELECT * FROM orders ORDER BY order_date DESC").fetchall()
    conn.close()

    return render_template('dashboard.html', orders=orders)


@app.route('/home', methods=['GET', 'POST'])
def home():
    if 'username' not in session or session['role'] != 'customer':
        return redirect(url_for('login'))

    product_prices = {
        "Laptop": 50000,
        "Headphones": 3000,
        "Mouse": 800,
        "Keyboard": 1500
    }

    conn = get_db()
    cursor = conn.cursor()

    if request.method == 'POST':
        if 'place_order' in request.form:
            product_name = request.form['product_name']
            total_amount = product_prices.get(product_name, 0)
            user_id = session['user_id']
            cursor.execute(
                "INSERT INTO orders (user_id, product_name, total_amount, status) VALUES (?, ?, ?, ?)",
                (user_id, product_name, total_amount, "Pending")
            )
            conn.commit()
            flash("Order placed successfully!", "success")
        elif 'delete_order' in request.form:
            order_id = request.form['order_id']
            cursor.execute("DELETE FROM orders WHERE order_id = ?", (order_id,))
            conn.commit()
            flash("Order deleted successfully!", "success")

    orders = cursor.execute("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC", (session['user_id'],)).fetchall()
    conn.close()

    return render_template('home.html', product_prices=product_prices, orders=orders)


@app.route('/logout')
def logout():
    session.clear()
    flash("Logged out successfully!", "info")
    return redirect(url_for('login'))


if __name__ == '__main__':
    app.run(debug=True)