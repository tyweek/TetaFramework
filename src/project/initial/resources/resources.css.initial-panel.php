/* public/assets/css/user_panel.css */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f2f2f2;
}

nav {
    background-color: #333;
    color: #fff;
    padding: 10px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.container {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    font-size: 24px;
    font-weight: bold;
}

.navbar-links {
    display: flex;
    align-items: center;
}

.navbar-links span {
    margin-right: 20px;
    color: #fff;
}

.navbar-links a.btn {
    background-color: #4CAF50;
    padding: 10px 20px;
    border-radius: 4px;
    color: #fff;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.navbar-links a.btn:hover {
    background-color: #45a049;
}

.toggle-btn {
    background-color: #4CAF50;
    color: #fff;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    display: none;
}

.sidebar {
    width: 250px;
    background-color: #333;
    color: #fff;
    position: fixed;
    top: 50px;
    bottom: 0;
    padding-top: 20px;
    overflow-y: auto;
    transition: transform 0.3s ease;
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    padding: 15px 20px;
}

.sidebar ul li a {
    color: #fff;
    text-decoration: none;
    display: block;
}

.sidebar ul li a:hover {
    background-color: #575757;
}

.main-content {
    margin-left: 250px;
    padding: 20px;
    transition: margin-left 0.3s ease;
}

.sidebar.active {
    transform: translateX(-250px);
}

.main-content.active {
    margin-left: 0;
}

@media (max-width: 768px) {
    .toggle-btn {
        display: block;
    }

    .sidebar {
        width: 100%;
        transform: translateX(-100%);
    }

    .main-content {
        margin-left: 0;
    }

    .sidebar.active {
        transform: translateX(0);
    }

    .main-content.active {
        margin-left: 250px;
    }
}
