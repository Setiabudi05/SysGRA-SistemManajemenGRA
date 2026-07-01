import pypdf
import os
import sys

def main():
    try:
        # Determine paths relative to script location
        base_dir = os.path.dirname(os.path.abspath(__file__))
        pdf_path = os.path.join(base_dir, "griya_rias_asmara.pdf")
        txt_path = os.path.join(base_dir, "storage", "app", "griya_rias_asmara.txt")

        if not os.path.exists(pdf_path):
            print(f"Error: {pdf_path} not found.")
            sys.exit(1)

        reader = pypdf.PdfReader(pdf_path)
        text = ""
        for page in reader.pages:
            page_text = page.extract_text()
            if page_text:
                text += page_text + "\n"

        os.makedirs(os.path.dirname(txt_path), exist_ok=True)
        with open(txt_path, "w", encoding="utf-8") as f:
            f.write(text)

        print("Success")
    except Exception as e:
        print(f"Error: {str(e)}")
        sys.exit(1)

if __name__ == "__main__":
    main()
