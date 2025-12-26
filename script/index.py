import os
import sys
import json
import base64
import mysql.connector
from dotenv import load_dotenv
from mistralai import Mistral

# Load environment variables
load_dotenv(dotenv_path=os.path.join(os.path.dirname(__file__), '../.env'))

def get_db_connection():
    return mysql.connector.connect(
        host=os.getenv("DB_HOST", "127.0.0.1"),
        user=os.getenv("DB_USERNAME", "root"),
        password=os.getenv("DB_PASSWORD", ""),
        database=os.getenv("DB_DATABASE", "dungeon_xplorer"),
        port=int(os.getenv("DB_PORT", 3306))
    )

def get_item_data(item_id):
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT id, name, icon FROM items WHERE id = %s", (item_id,))
    item = cursor.fetchone()
    conn.close()
    return item

def get_all_equipment_items():
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT id, name, icon FROM items WHERE type = 'equipment'")
    items = cursor.fetchall()
    conn.close()
    return items

def encode_image(image_path):
    try:
        with open(image_path, "rb") as image_file:
            return base64.b64encode(image_file.read()).decode('utf-8')
    except FileNotFoundError:
        print(f"Error: Image not found at {image_path}")
        return None

def update_item_stats(item_id, item_data):
    conn = get_db_connection()
    cursor = conn.cursor()
    
    sql = """
    UPDATE items 
    SET name = %s, weight = %s, price = %s, stat_ranges = %s 
    WHERE id = %s
    """
    
    stats_json = json.dumps(item_data['stats'])
    print(item_data['name'], item_data['weight'], item_data['price'], stats_json)
    cursor.execute(sql, (
        item_data['name'],
        item_data['weight'],
        item_data['price'],
        stats_json,
        item_id
    ))
    
    conn.commit()
    conn.close()
    print(f"Item {item_id} updated successfully.")

def generate_item(item_id, excluded_names=[]):
    print(f"Processing Item ID: {item_id}...")
    api_key = os.getenv("MISTRAL_API_KEY")
    if not api_key:
        print("Error: MISTRAL_API_KEY not found.")
        return None

    item = get_item_data(item_id)
    if not item:
        print(f"Item with ID {item_id} not found.")
        return None

    item_name = item['name']
    icon_path = item['icon']
    
    # Resolve image path
    base_path = os.path.join(os.path.dirname(__file__), '../public')
    full_image_path = os.path.join(base_path, icon_path) if icon_path else None
    
    base64_image = None
    if full_image_path:
        base64_image = encode_image(full_image_path)

    client = Mistral(api_key=api_key)

    recent_names_str = ", ".join(excluded_names[-20:]) # Keep context small, last 20 names

    prompt = f"""
    Analyse l'image et le nom actuel de l'item : "{item_name}".
    
    Tâche :
    1. Invente un NOUVEAU nom épique dans le style de DIABLO (Blizzard).
       - IMPORTANT : Utilise des NOMS PROPRES inventés (Héros, Dieux, Lieux mythiques) pour briser la monotonie.
       - INTERDICTION STRICTE d'utiliser les mots : "Ombre", "Ténèbres", "Éternelle", "Démoniaque", "Noire", "Sombre", "Maudit", "Vortex", "Solaire", "Tempête", "Rage", "Fureur". ILS SONT TROP UTILISÉS.
       - INTERDICTION de copier ces noms récents : [{recent_names_str}].
       
       Choisis une structure ALÉATOIRE parmi celles-ci :
       A. [Nom] de [Nom Propre] (ex: "Griffe de Griswold", "Regard de Tal Rasha")
       B. [Nom Propre], [Titre] (ex: "Windforce", "Messerschmidt, le Couperet")
       C. [Nom] [Adjectif] (ex: "Grand-Père", "Visage d'Andarielle")
       D. [Nom] du [Substantif Ruraux/Brutaux] (ex: "Hachoir du Boucher", "Peau du Ver Géant")
       
       Thèmes suggérés (Mélange-les !) : Entrailles, Sang, Jade, Céleste, Runique, Osseux, Spectral, Oublié, Ancien, Titan, Vorace.

    2. Poids : Estime un poids réaliste (en kg).
    3. Prix : Estime un prix en pièces d'or (entre 10 et 50000).
    4. Stats : Génère des min/max variés pour Force, Int, Dex, Vit.
       - Ne mets pas tout à 0 ou 1-5.
       - Spécialise l'objet (ex: Bottes lourdes = Force/Vit, Cape de mage = Int/Dex).
    
    Réponds UNIQUEMENT avec un JSON valide :
    {{
        "name": "Nom Créatif Ici",
        "weight": 2.5,
        "price": 1500,
        "stats": {{
            "strength": {{"min": 10, "max": 15}},
            "intelligence": {{"min": 0, "max": 2}},
            "dexterity": {{"min": 5, "max": 8}},
            "vitality": {{"min": 20, "max": 25}}
        }}
    }}
    """
    
    messages = [
        {
            "role": "user",
            "content": [
                {"type": "text", "text": prompt}
            ]
        }
    ]
    
    if base64_image:
        messages[0]["content"].append({
            "type": "image_url",
            "image_url": f"data:image/jpeg;base64,{base64_image}" 
        })
        print("Sending request with image...")
    else:
        print("Sending request without image (image not found or not set)...")

    try:
        chat_response = client.chat.complete(
            model="pixtral-12b-2409", 
            messages=messages,
            response_format={"type": "json_object"},
            temperature=1.2
        )

        content = chat_response.choices[0].message.content
        generated_data = json.loads(content)
        
        # print(json.dumps(generated_data, indent=4, ensure_ascii=False))
        
        update_item_stats(item_id, generated_data)
        return generated_data['name']

    except Exception as e:
        print(f"An error occurred processing Item {item_id}: {e}")
        return None

if __name__ == "__main__":
    if len(sys.argv) > 1:
        # Process specific item ID
        generate_item(sys.argv[1])
    else:
        # Process all equipment items
        print("No Item ID provided. Fetching all 'equipment' items...")
        items = get_all_equipment_items()
        print(f"Found {len(items)} items to process.")
        
        generated_names_history = []
        
        for item in items:
            new_name = generate_item(item['id'], generated_names_history)
            if new_name:
                generated_names_history.append(new_name)
                # Ensure list doesn't grow indefinitely, though for this script it's fine to keep growing or purge
                # keeping all for now to maximize variety check, or cap at 50 if context limit is an issue.
                if len(generated_names_history) > 100: 
                    generated_names_history.pop(0)

            print("-" * 30)
