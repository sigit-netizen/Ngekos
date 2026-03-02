import os
import re

file_path = r'd:\pkl\pkl\Ngekos\beckend\Ngekos\resources\views\welcome.blade.php'

if not os.path.exists(file_path):
    print(f"Error: {file_path} not found")
    exit(1)

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix the double class bug:
# it currently looks like: class="relative w-full overflow-hidden" class="relative w-full overflow-hidden aspect-[4/3]"
# or similar.
# We want just: class="relative w-full overflow-hidden aspect-[4/3]"

content = re.sub(r'class="relative w-full overflow-hidden"\s+class="relative w-full overflow-hidden aspect-\[4/3\]"', 'class="relative w-full overflow-hidden aspect-[4/3]"', content)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Fixed double class attribute bug")
