# 🔍 DataLens — CSV Analysis Studio

> A modern web-based CSV analysis platform for cleaning, exploring, analyzing datasets, and experimenting with machine learning.

## 🚀 Overview

**DataLens** is designed to make CSV data analysis easier for students, beginners, and developers.

Upload a CSV dataset and explore its structure, clean the data, view statistics, detect outliers, generate insights, and run machine learning models through a modern dark-themed interface.

## ✨ Features

- 📂 CSV Dataset Upload
- 🧹 Automatic Data Cleaning
- 📊 Dataset Overview
- 📈 Statistical Analysis
- 🔎 Outlier Detection
- 🤖 AI-Based Data Insights
- 🧠 Machine Learning
- 📋 Interactive Data Table
- 📑 Analysis Reports
- 💾 Download Analysis Results
- 🌙 Premium Dark UI

## 🧠 Machine Learning

### Regression
- Linear Regression
- Decision Tree Regressor
- Random Forest Regressor
- KNN Regressor

### Classification
- Logistic Regression
- Decision Tree Classifier
- Random Forest Classifier
- KNN Classifier
- Naive Bayes
- Support Vector Machine (SVM)

### Clustering
- K-Means

### Evaluation Metrics

- Mean Absolute Error (MAE)
- Mean Squared Error (MSE)
- Root Mean Squared Error (RMSE)
- Coefficient of Determination (R²)

## 🎯 Project Goals

DataLens aims to:

- Simplify CSV data analysis
- Help beginners understand datasets
- Reduce repetitive data-preparation work
- Provide an accessible environment for machine learning experiments
- Bring data analysis and machine learning together in one platform

## 🛠️ Technology Stack

- HTML5
- CSS3
- JavaScript
- CSV Data Processing
- Machine Learning Algorithms
- PHP
- MySQL
- XAMPP

## 📁 Project Structure

```text
DataLens/
├── frontend/
│   ├── index.html
│   ├── style.css
│   └── script.js
├── backend/
│   ├── config.php
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   ├── session.php
│   └── users.php
├── database.sql
├── assets/
└── README.md
```

> The exact structure may vary depending on the project version.

## 💻 Installation

### 1. Clone the repository

```bash
git clone https://github.com/YOUR-USERNAME/datalens.git
cd datalens
```

### 2. XAMPP Setup

For the PHP + MySQL version, place the project inside:

```text
C:\xampp\htdocs\DataLens
```

Start **Apache** and **MySQL** from XAMPP.

### 3. Database Setup

Open phpMyAdmin and import:

```text
database.sql
```

Then configure the database connection in:

```text
backend/config.php
```

### 4. Open the Application

```text
http://localhost/DataLens/
```

## 🔐 Security

The backend should follow secure authentication practices:

- Passwords are stored using secure password hashing.
- Prepared statements should be used for database queries.
- Sessions should be protected.
- Plain-text passwords should never be stored.

## 📸 Screenshots

You can add project screenshots inside a `docs` folder:

```text
docs/
├── dashboard.png
├── dataset.png
├── statistics.png
├── machine-learning.png
└── login.png
```

Then display them in this README:

```markdown
![DataLens Dashboard](docs/dashboard.png)
```

## 🔮 Future Improvements

- Advanced AI-powered dataset explanations
- More machine learning algorithms
- Automated model selection
- Advanced PDF reports
- Real-time visualizations
- Cloud dataset storage
- User analysis history
- Advanced prediction workflows

## 👨‍💻 Developer

**Ghana Krishna Sonowal**

BCA Student | Data Science Student

## ⭐ Support

If you find DataLens useful, consider giving the repository a ⭐ on GitHub.

---

### 📌 DataLens

**Upload → Clean → Analyze → Understand → Predict**
