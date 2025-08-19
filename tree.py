import os

def print_tree(path, prefix=""):
    """Verilen path dizinini ağaç şeklinde yazdırır."""
    if not os.path.exists(path):
        print("Dizin bulunamadı:", path)
        return

    items = os.listdir(path)
    pointers = ["├── "] * (len(items) - 1) + ["└── "]

    for pointer, item in zip(pointers, items):
        item_path = os.path.join(path, item)
        print(prefix + pointer + item)
        if os.path.isdir(item_path):
            extension = "│   " if pointer == "├── " else "    "
            print_tree(item_path, prefix + extension)

# Kodun bulunduğu dizini al
current_dir = os.getcwd()
print(f"Dizin ağacı: {current_dir}\n")
print_tree(current_dir)
