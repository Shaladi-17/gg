import fitz
import sys
import os
import glob

sys.stdout.reconfigure(encoding='utf-8')

folder = r'c:\Users\acer\Desktop\CS336'

# Find the Spring 2025 Midterm 1 file
for f in os.listdir(folder):
    if 'النصفي' in f and 'الاول' in f and 'ربيع' in f:
        filepath = os.path.join(folder, f)
        print(f"File: {f}")
        print("=" * 80)
        doc = fitz.open(filepath)
        for i, page in enumerate(doc):
            print(f"\n--- Page {i+1} ---")
            print(page.get_text())
        doc.close()
        break
