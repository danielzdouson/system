#!/usr/bin/env python3
"""
Generate PWA icons from source image
Requires: pip install Pillow
"""

from PIL import Image
import os

# Icon sizes required for PWA
SIZES = [72, 96, 128, 144, 152, 192, 384, 512]

# Input and output paths
INPUT_IMAGE = "gia_logo.png"  # Place your GIA logo here
OUTPUT_DIR = "public/images/icons"

def generate_icons():
    """Generate all required icon sizes from source image"""
    
    # Create output directory if it doesn't exist
    os.makedirs(OUTPUT_DIR, exist_ok=True)
    
    # Check if input image exists
    if not os.path.exists(INPUT_IMAGE):
        print(f"❌ Error: {INPUT_IMAGE} not found!")
        print(f"Please save your GIA logo as '{INPUT_IMAGE}' in the project root")
        return
    
    # Open source image
    try:
        img = Image.open(INPUT_IMAGE)
        print(f"✅ Loaded source image: {img.size[0]}x{img.size[1]} pixels")
        
        # Convert to RGBA if needed
        if img.mode != 'RGBA':
            img = img.convert('RGBA')
        
        # Generate each icon size
        for size in SIZES:
            # Create a new square image with white background
            icon = Image.new('RGBA', (size, size), (255, 255, 255, 255))
            
            # Resize source image to fit (with padding)
            img_resized = img.copy()
            img_resized.thumbnail((size, size), Image.Resampling.LANCZOS)
            
            # Center the image
            x = (size - img_resized.size[0]) // 2
            y = (size - img_resized.size[1]) // 2
            icon.paste(img_resized, (x, y), img_resized)
            
            # Save icon
            output_path = os.path.join(OUTPUT_DIR, f"icon-{size}x{size}.png")
            icon.save(output_path, "PNG", optimize=True)
            print(f"✅ Generated: icon-{size}x{size}.png")
        
        print(f"\n🎉 Successfully generated {len(SIZES)} icons in {OUTPUT_DIR}/")
        print("\nNext steps:")
        print("1. Verify icons look good")
        print("2. Test PWA installation in browser")
        print("3. Check browser DevTools > Application > Manifest")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return

if __name__ == "__main__":
    print("🎨 PWA Icon Generator for SACCO System")
    print("=" * 50)
    generate_icons()
