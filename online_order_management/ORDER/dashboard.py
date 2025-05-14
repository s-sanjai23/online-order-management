from flask import Flask, render_template, request, redirect, session, g, url_for
import sqlite3

app = Flask(__name__)
app.secret_key = 'your_secret_key'

DATABASE = 'online_order_management.db'

def get_db():
    db = getattr(g, '_database', None)
    if db is None:
        db = g._database = sqlite3.connect(DATABASE)
        db.row_factory = sqlite3.Row
    return db

@app.teardown_appcontext
def close_connection(exception):
    db = getattr(g, '_database', None)
    if db is not None:
        db.close()

@app.route('/dashboard', methods=['GET', 'POST'])
def dashboard():
    if 'username' not in session or session.get('role') != 'admin':
        return redirect(url_for('login'))

    message = ""
    db = get_db()

    # Handle order update
    if request.method == 'POST' and 'update_order' in request.form:
        order_id = request.form['order_id']
        new_status = request.form['status']

        try:
            db.execute("UPDATE orders SET status = ? WHERE order_id = ?", (new_status, order_id))
            db.commit()
            message = "✅ Order updated successfully!"
        except sqlite3.Error as e:
            message = f"❌ Error updating order: {e}"

    # Fetch orders
    orders = db.execute("SELECT * FROM orders ORDER BY order_date DESC").fetchall()

    return render_template('dashboard.html', username=session['username'], orders=orders, message=message)

@app.route('/login', methods=['GET', 'POST'])
def login():
    # Placeholder login route for session setup
    pass

if __name__ == '__main__':
    app.run(debug=True)