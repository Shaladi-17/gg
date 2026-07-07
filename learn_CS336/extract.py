import os, sys
from PyPDF2 import PdfReader

with open('all_lectures.txt', 'w', encoding='utf-8') as out:
    files = sorted([f for f in os.listdir('.') if f.endswith('.pdf')])
    for f in files:
        out.write(f'=== {f} ===\n')
        try:
            for page in PdfReader(f).pages:
                txt = page.extract_text()
                if txt:
                    out.write(txt + '\n')
        except Exception as e:
            out.write(f'Error: {e}\n')
        out.write('\n')
print("Done")
