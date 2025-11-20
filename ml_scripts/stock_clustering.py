#!/usr/bin/env python3
"""
Stock Clustering - Find Similar Stocks
=======================================
Groups stocks that behave similarly! Like finding twins in the stock market.

If you like BBCA, you might also like stocks in the same cluster!
"""

import sqlite3
import pandas as pd
import numpy as np
from datetime import datetime
import json

def load_latest_analyses(db_path='database/database.sqlite'):
    """Load most recent analysis for each stock"""
    print("📊 Loading latest stock analyses...")

    conn = sqlite3.connect(db_path)

    # Get most recent analysis for each symbol
    query = """
        SELECT *
        FROM stock_analyses sa1
        WHERE sa1.created_at = (
            SELECT MAX(sa2.created_at)
            FROM stock_analyses sa2
            WHERE sa2.symbol = sa1.symbol
        )
    """

    df = pd.read_sql_query(query, conn)
    conn.close()

    print(f"✅ Loaded {len(df)} stocks")
    return df

def cluster_stocks(df, n_clusters=5):
    """
    Group stocks into clusters based on their characteristics!

    Features used:
    - Technical indicators (RSI, volatility, swing)
    - Fundamentals (PE, PB, ROE)
    - Accumulation patterns (strength, institutional %)
    """
    print(f"\n🧠 Clustering stocks into {n_clusters} groups...")

    # Features for clustering
    feature_columns = [
        'rsi', 'volume_ratio', 'pe_ratio', 'pb_ratio', 'roe',
        'accumulation_strength', 'institutional_percent',
        'swing_score', 'avg_swing_percent', 'overall_score'
    ]

    # Remove stocks with missing data
    df_clean = df.dropna(subset=feature_columns).copy()

    if len(df_clean) < n_clusters:
        print(f"❌ Need at least {n_clusters} stocks with complete data.")
        print(f"   Currently have: {len(df_clean)} stocks")
        print("   🚀 Analyze more stocks to enable clustering!")
        return None

    X = df_clean[feature_columns]
    symbols = df_clean['symbol'].values

    print(f"📈 Clustering {len(X)} stocks...")

    try:
        from sklearn.preprocessing import StandardScaler
        from sklearn.cluster import KMeans

        # Normalize features (important for clustering!)
        scaler = StandardScaler()
        X_scaled = scaler.fit_transform(X)

        # K-Means clustering
        kmeans = KMeans(n_clusters=n_clusters, random_state=42, n_init=10)
        clusters = kmeans.fit_predict(X_scaled)

        # Add cluster labels to dataframe
        df_clean['cluster'] = clusters

        # Group by cluster
        results = {}

        print("\n🏷️  STOCK CLUSTERS:")
        print("=" * 60)

        for cluster_id in range(n_clusters):
            cluster_stocks = df_clean[df_clean['cluster'] == cluster_id]

            # Calculate cluster characteristics (averages)
            cluster_profile = {
                'avg_rsi': cluster_stocks['rsi'].mean(),
                'avg_pe': cluster_stocks['pe_ratio'].mean(),
                'avg_score': cluster_stocks['overall_score'].mean(),
                'avg_institutional': cluster_stocks['institutional_percent'].mean(),
                'avg_swing': cluster_stocks['swing_score'].mean(),
            }

            # Determine cluster "personality"
            personality = get_cluster_personality(cluster_profile)

            results[f'cluster_{cluster_id}'] = {
                'name': personality,
                'stocks': cluster_stocks['symbol'].tolist(),
                'count': len(cluster_stocks),
                'profile': cluster_profile
            }

            print(f"\n🏷️  Cluster {cluster_id}: {personality}")
            print(f"   Stocks ({len(cluster_stocks)}): {', '.join(cluster_stocks['symbol'].tolist())}")
            print(f"   Avg Score: {cluster_profile['avg_score']:.1f}")
            print(f"   Avg RSI: {cluster_profile['avg_rsi']:.1f}")
            print(f"   Avg PE: {cluster_profile['avg_pe']:.1f}")
            print(f"   Institutional %: {cluster_profile['avg_institutional']:.1f}%")

        return results

    except ImportError:
        print("⚠️  scikit-learn not installed. Installing...")
        import subprocess
        subprocess.check_call(['pip', 'install', '-r', 'ml_scripts/requirements.txt'])
        print("✅ Installed! Please run the script again.")
        return None

def get_cluster_personality(profile):
    """Give each cluster a fun name based on its characteristics!"""

    score = profile['avg_score']
    rsi = profile['avg_rsi']
    institutional = profile['avg_institutional']

    if score > 70 and institutional > 60:
        return "🌟 Blue Chip Stars (Strong + Institutional)"
    elif score > 70:
        return "🚀 Growth Champions (High Score)"
    elif rsi < 35:
        return "💎 Undervalued Gems (Low RSI)"
    elif rsi > 65:
        return "🔥 Overbought Hot Stocks"
    elif institutional > 60:
        return "🏦 Institutional Favorites"
    else:
        return "📊 Balanced Mixed Stocks"

def find_similar_stocks(symbol, cluster_data):
    """
    Find stocks similar to a given symbol!
    """
    print(f"\n🔍 Finding stocks similar to {symbol}...")

    # Find which cluster the symbol belongs to
    symbol_cluster = None
    for cluster_name, cluster_info in cluster_data.items():
        if symbol in cluster_info['stocks']:
            symbol_cluster = cluster_info
            print(f"   {symbol} is in: {cluster_info['name']}")
            break

    if symbol_cluster is None:
        print(f"   ❌ {symbol} not found in any cluster!")
        return []

    # Get other stocks in same cluster
    similar = [s for s in symbol_cluster['stocks'] if s != symbol]
    print(f"   Similar stocks: {', '.join(similar)}")

    return similar

def main():
    """Main execution"""
    print("=" * 60)
    print("🎯 STOCK CLUSTERING - Find Similar Stocks!")
    print("=" * 60)
    print("Groups stocks with similar characteristics!\n")

    # Load data
    df = load_latest_analyses()

    if len(df) == 0:
        print("❌ No data found! Use the dashboard to analyze some stocks first.")
        return

    # Cluster stocks
    n_clusters = min(5, max(2, len(df) // 3))  # Adaptive cluster count
    cluster_data = cluster_stocks(df, n_clusters=n_clusters)

    if cluster_data is not None:
        # Save results
        timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
        output_file = f'ml_scripts/stock_clusters_{timestamp}.json'

        with open(output_file, 'w') as f:
            json.dump(cluster_data, f, indent=2)

        print(f"\n💾 Results saved to: {output_file}")

        # Example: Find similar stocks to BBCA (if it exists)
        if 'BBCA' in df['symbol'].values or 'BBCA.JK' in df['symbol'].values:
            find_similar_stocks('BBCA', cluster_data)

    print("\n" + "=" * 60)
    print("✨ Clustering complete!")
    print("=" * 60)

if __name__ == "__main__":
    main()
