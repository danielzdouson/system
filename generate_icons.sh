#!/bin/bash
# Generate PWA icons from source image using ImageMagick

INPUT="gia_logo.png"
OUTPUT_DIR="public/images/icons"

# Icon sizes required for PWA
SIZES=(72 96 128 144 152 192 384 512)

echo "🎨 PWA Icon Generator for SACCO System"
echo "======================================"

# Check if ImageMagick is installed
if ! command -v convert &> /dev/null; then
    echo "❌ ImageMagick not found. Installing..."
    sudo apt-get update && sudo apt-get install -y imagemagick
fi

# Check if input image exists
if [ ! -f "$INPUT" ]; then
    echo "❌ Error: $INPUT not found!"
    echo "Please save your GIA logo as 'gia_logo.png' in the project root"
    exit 1
fi

# Create output directory
mkdir -p "$OUTPUT_DIR"

echo "✅ Source image: $INPUT"
echo ""

# Generate each icon size
for size in "${SIZES[@]}"; do
    output="$OUTPUT_DIR/icon-${size}x${size}.png"
    convert "$INPUT" -resize ${size}x${size} -background white -gravity center -extent ${size}x${size} "$output"
    echo "✅ Generated: icon-${size}x${size}.png"
done

echo ""
echo "🎉 Successfully generated ${#SIZES[@]} icons in $OUTPUT_DIR/"
echo ""
echo "Next steps:"
echo "1. Verify icons look good"
echo "2. Test PWA installation in browser"
echo "3. Check browser DevTools > Application > Manifest"
