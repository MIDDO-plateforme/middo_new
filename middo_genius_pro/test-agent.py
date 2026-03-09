# Test simple de MIDDO GENIUS PRO
import requests
import json

print("=" * 50)
print("   TEST MIDDO GENIUS PRO")
print("=" * 50)
print()

# Test 1: Health check
print("[TEST 1] Verification du serveur...")
try:
    response = requests.get("http://localhost:8000/health")
    print(f"Resultat: {response.json()}")
    print("[OK] Serveur en bonne sante!")
except Exception as e:
    print(f"[ERREUR] {e}")
    exit()

print()

# Test 2: Chat avec l'agent IA
print("[TEST 2] Discussion avec l'agent IA...")
print("Question: Comment optimiser MIDDO ?")
print()

data = {
    "message": "Donne-moi 3 conseils rapides pour optimiser MIDDO",
    "expertise": "fullstack",
    "context": "Symfony 6.3"
}

try:
    response = requests.post("http://localhost:8000/chat", json=data)
    result = response.json()
    
    print("[REPONSE DE L'AGENT IA]")
    print("-" * 50)
    print(result.get("response", "Pas de reponse"))
    print()
    print("[SUGGESTIONS]")
    for i, suggestion in enumerate(result.get("suggestions", []), 1):
        print(f"{i}. {suggestion}")
    print()
    print(f"[CONFIANCE] {result.get('confidence', 0) * 100}%")
    
except Exception as e:
    print(f"[ERREUR] {e}")
    if "401" in str(e) or "Unauthorized" in str(e):
        print("Probleme avec la cle OpenAI!")
    elif "500" in str(e):
        print("Erreur serveur, verifie les logs PowerShell")

print()
print("=" * 50)
input("Appuie sur Entree pour fermer...")
