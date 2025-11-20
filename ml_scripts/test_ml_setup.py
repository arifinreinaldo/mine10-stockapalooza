#!/usr/bin/env python3
"""
Quick test to verify ML setup is working!
"""

import sqlite3
import os

def test_database():
    """Check if database and table exist"""
    print("🧪 Testing ML Setup...")
    print("=" * 50)

    db_path = 'database/database.sqlite'

    # Check if database exists
    if not os.path.exists(db_path):
        print("❌ Database file not found!")
        print(f"   Expected: {db_path}")
        return False

    print(f"✅ Database file exists: {db_path}")

    # Check if table exists
    conn = sqlite3.connect(db_path)
    cursor = conn.cursor()

    try:
        cursor.execute("SELECT COUNT(*) FROM stock_analyses")
        count = cursor.fetchone()[0]
        print(f"✅ stock_analyses table exists!")
        print(f"   Current records: {count}")

        if count == 0:
            print("\n📝 No data yet! To collect data:")
            print("   1. Open the dashboard: http://localhost:8000/dashboard")
            print("   2. Analyze some stocks (BBCA, AAPL, etc.)")
            print("   3. Data will be auto-saved!")
        else:
            print(f"\n🎉 Great! You have {count} analyses saved!")
            print("   You can now run ML scripts:")
            print("   - python3 ml_scripts/feature_importance.py")
            print("   - python3 ml_scripts/stock_clustering.py")

        # Show sample data
        if count > 0:
            cursor.execute("""
                SELECT symbol, market, price, rsi, overall_score, created_at
                FROM stock_analyses
                ORDER BY created_at DESC
                LIMIT 5
            """)

            print("\n📊 Latest 5 analyses:")
            print("-" * 50)
            for row in cursor.fetchall():
                symbol, market, price, rsi, score, date = row
                print(f"   {symbol:8s} | Price: {price:10.2f} | RSI: {rsi or 'N/A':>5} | Score: {score:5.1f} | {date[:10]}")

        conn.close()
        return True

    except sqlite3.OperationalError as e:
        print(f"❌ Table doesn't exist: {e}")
        print("\n🔧 Fix: Run migrations:")
        print("   php artisan migrate --force")
        conn.close()
        return False

def check_python_packages():
    """Check if required Python packages are installed"""
    print("\n🐍 Checking Python packages...")
    print("-" * 50)

    packages = {
        'pandas': 'Data manipulation',
        'numpy': 'Numerical computing',
        'sklearn': 'Machine learning (scikit-learn)',
        'xgboost': 'Gradient boosting'
    }

    all_installed = True

    for package, description in packages.items():
        try:
            __import__(package)
            print(f"✅ {package:12s} - {description}")
        except ImportError:
            print(f"❌ {package:12s} - NOT INSTALLED")
            all_installed = False

    if not all_installed:
        print("\n📦 To install missing packages:")
        print("   pip install -r ml_scripts/requirements.txt")
    else:
        print("\n✅ All packages installed!")

    return all_installed

def main():
    print("\n" + "=" * 50)
    print("🚀 ML SETUP TEST")
    print("=" * 50 + "\n")

    db_ok = test_database()
    packages_ok = check_python_packages()

    print("\n" + "=" * 50)
    if db_ok and packages_ok:
        print("✨ Everything looks good!")
        print("=" * 50)
        print("\n🎯 Next steps:")
        print("   1. Analyze more stocks on dashboard")
        print("   2. Wait 7 days for historical data")
        print("   3. Run: python3 ml_scripts/feature_importance.py")
        print("   4. Run: python3 ml_scripts/stock_clustering.py")
    else:
        print("⚠️  Please fix the issues above")
        print("=" * 50)

if __name__ == "__main__":
    main()
