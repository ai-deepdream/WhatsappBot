# Module Imports
import os
from PIL import Image
#import pytesseract
from pdf2image import convert_from_path
#from pyzbar import pyzbar
from pyzbar.pyzbar import decode

# Define Paths
poppler_path = r'C:\Program Files\poppler-0.68.0\bin'
#pytesseract.pytesseract.tesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract'

pdf_path = "C01-1084.pdf"

# Save PDF pages to images
images = convert_from_path(pdf_path=pdf_path, poppler_path=poppler_path)
for count, img in enumerate(images):
    img_name = f"page_{count}.png"  
    img.save(img_name, "png")

# Barcode reader
picture = Image.open(img_name)
barcode = decode(picture)
text = barcode[0].data.decode()
print("Text : {}".format(text))