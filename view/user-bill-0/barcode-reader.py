from PIL import Image
from pyzbar.pyzbar import decode
import os
from sys import argv
from urllib.parse import unquote
# dir = os.path.dirname(os.path.realpath(__file__))

try:
    argv[1]
except IndexError:
    exit()

imgName = argv[1].replace('+', ' ')
if not os.path.exists(imgName):
    print("None")
    exit()
picture = Image.open(imgName)
barcode = decode(picture)
text = barcode[0].data.decode()
print(format(text))

rotatedImage = picture.transpose(Image.Transpose.ROTATE_90)
rotatedImage.save(imgName)