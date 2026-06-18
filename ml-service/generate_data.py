"""
generate_data.py
Membuat data dummy harga alpukat historis untuk training model Random Forest.
Data dibuat dengan pola musiman (harga naik saat bukan musim panen, turun saat panen raya)
ditambah tren kenaikan harga jangka panjang dan sedikit noise acak agar realistis.
"""
import pandas as pd
import numpy as np
from datetime import datetime, timedelta

np.random.seed(42)

# Jenis alpukat yang dijual di Kampung Alpukat
JENIS_ALPUKAT = ["Miki", "Mentega", "Wina", "Aligator", "Kendil"]

# Harga dasar per jenis (Rp/kg)
HARGA_DASAR = {
    "Miki": 32000,
    "Mentega": 28000,
    "Wina": 42000,
    "Aligator": 25000,
    "Kendil": 35000,
}

def musim_panen_factor(bulan):
    """
    Musim panen raya alpukat di Indonesia umumnya Februari-April dan Agustus-Oktober.
    Saat panen raya, suplai melimpah -> harga turun.
    Saat bukan musim, suplai terbatas -> harga naik.
    """
    musim_raya = [2, 3, 4, 8, 9, 10]
    if bulan in musim_raya:
        return np.random.uniform(0.75, 0.88)   # harga turun 12-25%
    else:
        return np.random.uniform(1.05, 1.25)   # harga naik 5-25%

def generate_data(start_date="2021-01-01", end_date="2026-06-01"):
    rows = []
    dates = pd.date_range(start=start_date, end=end_date, freq="W-MON")  # data mingguan, tiap Senin

    for jenis in JENIS_ALPUKAT:
        harga_dasar = HARGA_DASAR[jenis]
        for i, tgl in enumerate(dates):
            bulan = tgl.month
            tahun = tgl.year

            # Tren kenaikan harga jangka panjang (inflasi/permintaan naik ~4% per tahun)
            tahun_ke = (tgl - dates[0]).days / 365.0
            tren_factor = 1 + (0.04 * tahun_ke)

            # Faktor musiman panen
            musim_factor = musim_panen_factor(bulan)

            # Curah hujan dummy (mm) - mempengaruhi hasil panen & harga
            curah_hujan = np.clip(np.random.normal(180, 80), 20, 400)
            # Curah hujan ekstrem (terlalu kering/basah) sedikit menaikkan harga
            hujan_factor = 1.0
            if curah_hujan < 60 or curah_hujan > 320:
                hujan_factor = np.random.uniform(1.03, 1.10)

            # Jumlah pasokan (kg) - berbanding terbalik dengan harga
            pasokan = np.clip(np.random.normal(800, 250) * (2 - musim_factor), 100, 2500)

            # Noise acak kecil
            noise = np.random.normal(1, 0.04)

            harga = harga_dasar * tren_factor * musim_factor * hujan_factor * noise
            harga = round(harga / 500) * 500  # bulatkan ke kelipatan 500

            rows.append({
                "tanggal": tgl.strftime("%Y-%m-%d"),
                "jenis_alpukat": jenis,
                "bulan": bulan,
                "tahun": tahun,
                "curah_hujan_mm": round(curah_hujan, 1),
                "pasokan_kg": round(pasokan, 1),
                "musim_panen": 1 if bulan in [2, 3, 4, 8, 9, 10] else 0,
                "harga_per_kg": int(harga),
            })

    df = pd.DataFrame(rows)
    return df

if __name__ == "__main__":
    df = generate_data()
    df.to_csv("data/harga_alpukat_dummy.csv", index=False)
    print(f"✅ Data dummy berhasil dibuat: {len(df)} baris")
    print(f"   Rentang tanggal: {df['tanggal'].min()} s/d {df['tanggal'].max()}")
    print(f"   Jenis alpukat: {df['jenis_alpukat'].unique().tolist()}")
    print("\nContoh 5 baris pertama:")
    print(df.head())
