#!/usr/bin/env python3
"""
Feature Importance Analysis
============================
Finds which stock indicators matter MOST for predicting price movements!

Like finding out: Does RSI matter more than PE ratio? Does volume beat fundamentals?
"""

import sqlite3
import pandas as pd
import numpy as np
from datetime import datetime, timedelta
import json

def load_data_from_db(db_path='database/database.sqlite'):
    """Load stock analysis data from SQLite database"""
    print("📊 Loading data from database...")

    conn = sqlite3.connect(db_path)

    # Load all stock analyses
    query = """
        SELECT * FROM stock_analyses
        WHERE created_at >= datetime('now', '-30 days')
        ORDER BY created_at DESC
    """

    df = pd.read_sql_query(query, conn)
    conn.close()

    print(f"✅ Loaded {len(df)} stock analyses from last 30 days")
    return df

def calculate_future_labels(df):
    """
    Calculate if stock went UP after 7 days
    This is what ML will try to predict!

    Note: We can only do this for OLD data (7+ days ago)
    """
    print("\n🎯 Calculating future price movements...")

    labeled_data = []

    for symbol in df['symbol'].unique():
        symbol_data = df[df['symbol'] == symbol].sort_values('created_at')

        for i in range(len(symbol_data) - 1):
            current_row = symbol_data.iloc[i]

            # Find price 7 days later
            current_date = pd.to_datetime(current_row['created_at'])
            future_date = current_date + timedelta(days=7)

            # Look for analysis within 7-10 days
            future_rows = symbol_data[
                (pd.to_datetime(symbol_data['created_at']) >= future_date) &
                (pd.to_datetime(symbol_data['created_at']) <= future_date + timedelta(days=3))
            ]

            if len(future_rows) > 0:
                future_price = future_rows.iloc[0]['price']
                current_price = current_row['price']

                # Calculate price change
                price_change = ((future_price - current_price) / current_price) * 100
                went_up_5pct = price_change >= 5

                # Add to labeled data
                row_dict = current_row.to_dict()
                row_dict['price_after_7days'] = future_price
                row_dict['price_change_7days_percent'] = price_change
                row_dict['went_up_5pct'] = went_up_5pct
                labeled_data.append(row_dict)

    labeled_df = pd.DataFrame(labeled_data)
    print(f"✅ Labeled {len(labeled_df)} samples with future outcomes")

    return labeled_df

def analyze_feature_importance(df):
    """
    Use XGBoost to find which features matter most!
    🎯 Target: Will stock go up 5% in next 7 days?
    """
    print("\n🧠 Training ML model to find important features...")

    # Features to analyze
    feature_columns = [
        'rsi', 'above_sma', 'volume_ratio',
        'pe_ratio', 'pb_ratio', 'roe', 'eps',
        'accumulation_strength', 'accumulation_days', 'institutional_percent',
        'swing_score', 'avg_swing_percent', 'overall_score'
    ]

    # Remove rows with missing data
    df_clean = df.dropna(subset=feature_columns + ['went_up_5pct'])

    if len(df_clean) < 10:
        print("❌ Not enough data yet! Need at least 10 labeled samples.")
        print(f"   Currently have: {len(df_clean)} samples")
        print("   🚀 Keep using the dashboard to collect more data!")
        return None

    # Prepare features and target
    X = df_clean[feature_columns]
    y = df_clean['went_up_5pct'].astype(int)

    print(f"📈 Training on {len(X)} samples...")
    print(f"   Positive (went up 5%): {y.sum()}")
    print(f"   Negative (didn't go up 5%): {len(y) - y.sum()}")

    # Train XGBoost model
    try:
        from xgboost import XGBClassifier

        model = XGBClassifier(
            n_estimators=50,
            max_depth=3,
            learning_rate=0.1,
            random_state=42,
            use_label_encoder=False,
            eval_metric='logloss'
        )

        model.fit(X, y)

        # Get feature importance
        importance = pd.DataFrame({
            'feature': feature_columns,
            'importance': model.feature_importances_
        }).sort_values('importance', ascending=False)

        print("\n🏆 FEATURE IMPORTANCE RANKINGS:")
        print("=" * 50)
        for idx, row in importance.iterrows():
            bar = "█" * int(row['importance'] * 50)
            print(f"{row['feature']:25s} {bar} {row['importance']:.3f}")

        # Accuracy
        from sklearn.metrics import accuracy_score
        predictions = model.predict(X)
        accuracy = accuracy_score(y, predictions)
        print(f"\n✅ Model Accuracy: {accuracy*100:.1f}%")

        return importance

    except ImportError:
        print("⚠️  XGBoost not installed. Installing...")
        import subprocess
        subprocess.check_call(['pip', 'install', '-r', 'ml_scripts/requirements.txt'])
        print("✅ Installed! Please run the script again.")
        return None

def main():
    """Main execution"""
    print("=" * 60)
    print("🚀 STOCK FEATURE IMPORTANCE ANALYZER")
    print("=" * 60)
    print("This finds which indicators predict stock success!\n")

    # Load data
    df = load_data_from_db()

    if len(df) == 0:
        print("❌ No data found! Use the dashboard to analyze some stocks first.")
        return

    # Calculate future labels
    labeled_df = calculate_future_labels(df)

    if len(labeled_df) == 0:
        print("❌ Not enough historical data to calculate future outcomes.")
        print("   Need data that is at least 7 days old!")
        print("   🚀 Keep analyzing stocks and come back in a week!")
        return

    # Analyze importance
    importance = analyze_feature_importance(labeled_df)

    if importance is not None:
        # Save results
        timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
        output_file = f'ml_scripts/feature_importance_{timestamp}.json'

        importance_dict = importance.to_dict('records')
        with open(output_file, 'w') as f:
            json.dump(importance_dict, f, indent=2)

        print(f"\n💾 Results saved to: {output_file}")

    print("\n" + "=" * 60)
    print("✨ Analysis complete!")
    print("=" * 60)

if __name__ == "__main__":
    main()
