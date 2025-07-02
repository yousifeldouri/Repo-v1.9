from PIL import Image

# افتح صورة PNG
img = Image.open('input.png')

# احفظ الصورة بصيغة .ico
img.save('output.ico', format='ICO')
