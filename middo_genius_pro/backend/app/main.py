# MIDDO GENIUS PRO - Serveur Principal FastAPI
# Version 1.1 - CORRIGÉ pour OpenAI v1.0+

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import Optional, List
from openai import OpenAI
import os
from dotenv import load_dotenv

# Chargement des variables d'environnement
load_dotenv()

# Configuration OpenAI v1.0+
client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))

# Initialisation FastAPI
app = FastAPI(
    title="MIDDO GENIUS PRO",
    description="Agent IA Multi-Expertise pour MIDDO",
    version="1.1.0"
)

# Configuration CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Modeles de donnees
class ChatRequest(BaseModel):
    message: str
    expertise: str
    context: Optional[str] = None
    user_id: Optional[str] = None

class ChatResponse(BaseModel):
    response: str
    expertise: str
    confidence: float
    suggestions: List[str]

# Systeme de prompts par expertise
EXPERTISE_PROMPTS = {
    "ai_ml": "Tu es un expert en IA et Machine Learning specialise dans l'optimisation de modeles, le fine-tuning et l'architecture de systemes IA. Tu travailles sur MIDDO (Symfony 6.3, PHP 8.3).",
    "fullstack": "Tu es un developpeur Full-Stack expert en Symfony 6.3, PHP 8.3, Doctrine ORM, Tailwind CSS et Alpine.js. Tu connais parfaitement l'architecture MIDDO.",
    "security": "Tu es un architecte securite specialise dans les audits de securite, le chiffrement AES-256, JWT et la protection contre OWASP Top 10.",
    "business": "Tu es un strategiste business expert en analyse de marche, modeles economiques SaaS, growth hacking et levee de fonds.",
    "finance": "Tu es un conseiller financier specialise dans la gestion budgetaire startup, calcul ROI, KPIs financiers et levee de fonds.",
    "training": "Tu es un formateur expert en creation de tutoriels, documentation technique et formation utilisateurs."
}

MIDDO_CONTEXT = """
MIDDO est une plateforme collaborative avec Symfony 6.3, PHP 8.3, PostgreSQL, Redis.
4 APIs IA actives: Chatbot, Suggestions, Matching, Sentiment Analysis.
Session 21: Recherche avancee multi-criteres.
"""

@app.get("/")
async def root():
    return {
        "service": "MIDDO GENIUS PRO",
        "version": "1.1.0",
        "status": "operational",
        "expertises": list(EXPERTISE_PROMPTS.keys())
    }

@app.get("/health")
async def health_check():
    api_key = os.getenv("OPENAI_API_KEY")
    return {
        "status": "healthy",
        "openai_configured": bool(api_key and api_key.startswith("sk-"))
    }

@app.post("/chat", response_model=ChatResponse)
async def chat(request: ChatRequest):
    try:
        # Selection du prompt d'expertise
        system_prompt = EXPERTISE_PROMPTS.get(request.expertise)
        if not system_prompt:
            raise HTTPException(status_code=400, detail="Expertise invalide")
        
        # Construction du contexte complet
        full_context = f"{system_prompt}\n\n{MIDDO_CONTEXT}"
        if request.context:
            full_context += f"\n\nContexte: {request.context}"
        
        # Appel OpenAI v1.0+ (CORRIGÉ)
        response = client.chat.completions.create(
            model="gpt-3.5-turbo",
            messages=[
                {"role": "system", "content": full_context},
                {"role": "user", "content": request.message}
            ],
            temperature=0.7,
            max_tokens=500
        )
        
        ai_response = response.choices[0].message.content
        
        # Generation de suggestions
        suggestions = generate_suggestions(request.expertise)
        
        return ChatResponse(
            response=ai_response,
            expertise=request.expertise,
            confidence=0.95,
            suggestions=suggestions
        )
        
    except Exception as e:
        print(f"ERREUR: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Erreur: {str(e)}")

def generate_suggestions(expertise: str) -> List[str]:
    suggestions_map = {
        "ai_ml": ["Optimiser les performances", "Implementer du caching", "Ajouter monitoring"],
        "fullstack": ["Ajouter tests unitaires", "Optimiser SQL", "Caching Redis"],
        "security": ["Audit securite", "Renforcer auth", "Logs audit"],
        "business": ["Analyser metriques", "Definir OKRs", "Etudier concurrence"],
        "finance": ["Calculer burn rate", "Projeter financement", "Optimiser couts"],
        "training": ["Creer tutoriels video", "FAQ interactive", "Onboarding guide"]
    }
    return suggestions_map.get(expertise, ["Explorer options", "Demander details"])

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
