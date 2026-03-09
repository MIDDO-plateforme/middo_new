document.addEventListener("DOMContentLoaded", () => {

    const input = document.getElementById("middo-search-input");
    const resultsBox = document.getElementById("middo-search-results");

    if (!input) return;

    // --- Contexte pipeline multi-slots ---
    const createEmptyContext = () => ({
        output: null,
        summary: null,
        translation: null,
        note: null,
        message: null,
        lastAction: null,
        meta: {}
    });

    // --- Historique local ---
    let history = JSON.parse(localStorage.getItem("middo_search_history") || "[]");

    function addToHistory(query) {
        query = query.trim();
        if (!query) return;

        history = history.filter(h => h !== query);
        history.unshift(query);
        history = history.slice(0, 10);

        localStorage.setItem("middo_search_history", JSON.stringify(history));
    }

    // --- Commandes système avancées ---
    const systemActions = [
        { keywords: ["vider notifications", "clear notifications"], action: (ctx) => clearNotifications(ctx) },
        { keywords: ["fermer toutes les fenêtres", "close all windows"], action: (ctx) => closeAllWindows(ctx) },
        { keywords: ["mode sombre", "dark mode"], action: (ctx) => enableDarkMode(ctx) },
        { keywords: ["mode clair", "light mode"], action: (ctx) => disableDarkMode(ctx) },
        { keywords: ["activer widgets"], action: (ctx) => enableWidgets(ctx) },
        { keywords: ["désactiver widgets"], action: (ctx) => disableWidgets(ctx) },
        { keywords: ["recharger", "reload"], action: (ctx) => { ctx.lastAction = "reload"; location.reload(); } },
        { keywords: ["ouvrir météo", "widget météo"], action: (ctx) => openWeatherWidget(ctx) }
    ];

    // --- Commandes système (ouverture d'apps) ---
    const systemCommands = [
        { keywords: ["documents", "fichiers"], app: "Documents" },
        { keywords: ["notifications"], app: "Notifications" },
        { keywords: ["paramètres", "settings"], app: "Paramètres" },
        { keywords: ["ia", "assistant"], app: "IA" },
        { keywords: ["messages", "chat"], app: "Messages" },
        { keywords: ["notes"], app: "Notes" },
        { keywords: ["calendrier"], app: "Calendrier" }
    ];

    // --- Workflows IA intelligents (unitaires) ---
    const workflowPatterns = [
        {
            match: /^nouvelle note\s*:(.+)$/i,
            handler: async (ctx, full, content) => {
                ctx.note = content.trim();
                ctx.output = ctx.note;
                ctx.lastAction = "create_note";
                await createNoteWorkflow(ctx);
            }
        },
        {
            match: /^nouveau message\s*:(.+)$/i,
            handler: async (ctx, full, content) => {
                ctx.message = content.trim();
                ctx.output = ctx.message;
                ctx.lastAction = "create_message";
                await createMessageWorkflow(ctx);
            }
        },
        {
            match: /^résumer\s*:(.+)$/i,
            handler: async (ctx, full, content) => {
                ctx.summary = await quickSummarize(content.trim());
                ctx.output = ctx.summary;
                ctx.lastAction = "summary";
            }
        },
        {
            match: /^traduire en anglais\s*:(.+)$/i,
            handler: async (ctx, full, content) => {
                ctx.translation = await quickTranslateTo("anglais", content.trim());
                ctx.output = ctx.translation;
                ctx.lastAction = "translation_en";
            }
        },
        {
            match: /^traduire en français\s*:(.+)$/i,
            handler: async (ctx, full, content) => {
                ctx.translation = await quickTranslateTo("français", content.trim());
                ctx.output = ctx.translation;
                ctx.lastAction = "translation_fr";
            }
        }
    ];

    // --- Actions rapides (affichage palette) ---
    const quickActions = [
        { keywords: ["nouvelle note", "new note"], label: "Nouvelle note", action: () => openQuickNote() },
        { keywords: ["nouveau message", "new message"], label: "Nouveau message", action: () => openQuickMessage() },
        { keywords: ["résumer", "resume"], label: "Résumer un texte", action: (q) => quickSummarize(q.replace(/résumer/gi, "").trim()) },
        { keywords: ["traduire", "translate"], label: "Traduire un texte", action: (q) => quickTranslate(q.replace(/traduire/gi, "").trim()) }
    ];

    // --- Apps ---
    const appSuggestions = [
        "Documents",
        "Notifications",
        "Paramètres",
        "IA",
        "Messages",
        "Notes",
        "Calendrier"
    ];

    let currentIndex = -1;

    function trySystemCommand(query, ctx) {
        const q = query.toLowerCase();

        for (const cmd of systemCommands) {
            if (cmd.keywords.some(k => q.includes(k))) {
                const icon = document.querySelector(`.os-icon[data-app="${cmd.app}"]`);
                if (icon) {
                    icon.click();
                    ctx.lastAction = "open_app:" + cmd.app;
                }
                return true;
            }
        }

        return false;
    }

    function trySystemAction(query, ctx) {
        const q = query.toLowerCase();

        for (const act of systemActions) {
            if (act.keywords.some(k => q.includes(k))) {
                act.action(ctx);
                return true;
            }
        }

        return false;
    }

    function tryQuickAction(query) {
        const q = query.toLowerCase();

        for (const act of quickActions) {
            if (act.keywords.some(k => q.startsWith(k))) {
                act.action(query);
                return true;
            }
        }

        return false;
    }

    async function tryWorkflowUnit(query, ctx) {
        for (const wf of workflowPatterns) {
            const match = query.match(wf.match);
            if (match) {
                await wf.handler(ctx, match[0], match[1] || "");
                return true;
            }
        }
        return false;
    }

    // --- Pipeline multi-étapes ---
    function splitIntoSteps(query) {
        // découpe sur " puis " ou ", puis "
        return query
            .split(/,\s*puis\s+| puis /i)
            .map(s => s.trim())
            .filter(Boolean);
    }

    async function runPipeline(query, ctx) {
        const steps = splitIntoSteps(query);

        if (steps.length === 0) return false;

        for (const step of steps) {
            const stepLower = step.toLowerCase();

            // 1) Workflows unitaires (résumer, traduire, nouvelle note, etc.)
            if (await tryWorkflowUnit(step, ctx)) {
                continue;
            }

            // 2) Si la phrase contient "avec le résumé" ou "avec la traduction"
            if (/avec le résumé/i.test(stepLower) && ctx.summary) {
                // ex: "crée une note avec le résumé"
                if (/note/i.test(stepLower)) {
                    ctx.note = ctx.summary;
                    ctx.output = ctx.summary;
                    ctx.lastAction = "create_note_from_summary";
                    await createNoteWorkflow(ctx);
                    continue;
                }
                if (/message/i.test(stepLower)) {
                    ctx.message = ctx.summary;
                    ctx.output = ctx.summary;
                    ctx.lastAction = "create_message_from_summary";
                    await createMessageWorkflow(ctx);
                    continue;
                }
            }

            if (/avec la traduction/i.test(stepLower) && ctx.translation) {
                if (/note/i.test(stepLower)) {
                    ctx.note = ctx.translation;
                    ctx.output = ctx.translation;
                    ctx.lastAction = "create_note_from_translation";
                    await createNoteWorkflow(ctx);
                    continue;
                }
                if (/message/i.test(stepLower)) {
                    ctx.message = ctx.translation;
                    ctx.output = ctx.translation;
                    ctx.lastAction = "create_message_from_translation";
                    await createMessageWorkflow(ctx);
                    continue;
                }
            }

            // 3) Commandes système / actions système
            if (trySystemCommand(step, ctx)) continue;
            if (trySystemAction(step, ctx)) continue;

            // 4) Actions rapides simples
            if (tryQuickAction(step)) continue;

            // 5) Fallback IA sur l'étape
            await runSingleIA(step, ctx);
        }

        return true;
    }

    async function runSingleIA(prompt, ctx) {
        try {
            const res = await fetch("/api/ia", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ prompt })
            });

            const data = await res.json();
            ctx.output = data.answer || ctx.output;
            ctx.lastAction = "ia_step";

            resultsBox.style.display = "block";
            resultsBox.innerHTML = renderCategory("IA", `
                <div class="middo-search-result-item middo-search-result-ia">
                    ${ctx.output || "Aucune réponse."}
                </div>
            `);
        } catch (err) {
            resultsBox.style.display = "block";
            resultsBox.innerHTML = renderCategory("IA", `
                <div class="middo-search-result-item">Erreur de communication.</div>
            `);
        }
    }

    // --- Debounce IA ---
    let debounceTimer = null;

    function debounce(callback, delay = 400) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(callback, delay);
    }

    // --- Rendu catégories ---
    function renderCategory(title, itemsHtml) {
        if (!itemsHtml.trim()) return "";
        return `
            <div class="middo-search-section-title">${title}</div>
            ${itemsHtml}
        `;
    }

    // --- Auto-complétion + Historique + IA live ---
    input.addEventListener("input", () => {
        const q = input.value.trim().toLowerCase();
        currentIndex = -1;

        if (!q) {
            if (history.length > 0) {
                const historyHtml = history.map(h => `
                    <div class="middo-search-result-item" data-history="${h}">
                        ${h}
                    </div>
                `).join("");

                resultsBox.style.display = "block";
                resultsBox.innerHTML = renderCategory("Historique", historyHtml);

                document.querySelectorAll("[data-history]").forEach(item => {
                    item.addEventListener("click", () => {
                        input.value = item.getAttribute("data-history");
                        input.dispatchEvent(new KeyboardEvent("keydown", { key: "Enter" }));
                    });
                });
            } else {
                resultsBox.style.display = "none";
            }

            return;
        }

        // Apps
        const appsHtml = appSuggestions
            .filter(app => app.toLowerCase().includes(q))
            .map(app => `
                <div class="middo-search-result-item" data-suggest="${app}">
                    ${app}
                </div>
            `).join("");

        // Actions rapides
        const quickHtml = quickActions
            .filter(a => a.keywords.some(k => q.startsWith(k)))
            .map(a => `
                <div class="middo-search-result-item" data-quick="${a.label}">
                    ⚡ ${a.label}
                </div>
            `).join("");

        // Actions système
        const sysHtml = systemActions
            .filter(a => a.keywords.some(k => q.includes(k)))
            .map(a => `
                <div class="middo-search-result-item" data-sys="${a.keywords[0]}">
                    🛠️ ${a.keywords[0]}
                </div>
            `).join("");

        const iaPlaceholder = `
            <div class="middo-search-result-item">Analyse IA…</div>
        `;

        resultsBox.style.display = "block";
        resultsBox.innerHTML =
            renderCategory("Apps", appsHtml) +
            renderCategory("Actions rapides", quickHtml) +
            renderCategory("Système", sysHtml) +
            renderCategory("Suggestions IA", iaPlaceholder);

        // Clic apps
        document.querySelectorAll("[data-suggest]").forEach(item => {
            item.addEventListener("click", () => {
                const app = item.getAttribute("data-suggest");
                const icon = document.querySelector(`.os-icon[data-app="${app}"]`);
                if (icon) icon.click();
                resultsBox.style.display = "none";
                input.value = "";
            });
        });

        // Clic actions rapides
        document.querySelectorAll("[data-quick]").forEach(item => {
            item.addEventListener("click", () => {
                const label = item.getAttribute("data-quick");
                const action = quickActions.find(a => a.label === label);
                if (action) action.action(q);
                resultsBox.style.display = "none";
                input.value = "";
            });
        });

        // Clic actions système
        document.querySelectorAll("[data-sys]").forEach(item => {
            item.addEventListener("click", () => {
                const keyword = item.getAttribute("data-sys");
                const action = systemActions.find(a => a.keywords.includes(keyword));
                if (action) action.action(createEmptyContext());
                resultsBox.style.display = "none";
                input.value = "";
            });
        });

        // Suggestions IA en direct
        debounce(async () => {
            try {
                const res = await fetch("/api/ia", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ prompt: "Complétion rapide : " + q })
                });

                const data = await res.json();
                const suggestion = data.answer || "Aucune suggestion IA.";

                const iaHtml = `
                    <div class="middo-search-result-item middo-search-result-ia" data-ia-suggest="${suggestion}">
                        ${suggestion}
                    </div>
                `;

                const before = resultsBox.innerHTML.split("Suggestions IA")[0];

                resultsBox.innerHTML =
                    before +
                    renderCategory("Suggestions IA", iaHtml);

                document.querySelectorAll("[data-ia-suggest]").forEach(item => {
                    item.addEventListener("click", () => {
                        input.value = item.getAttribute("data-ia-suggest");
                        input.dispatchEvent(new KeyboardEvent("keydown", { key: "Enter" }));
                    });
                });

            } catch (err) {}
        });

    });

    // --- Navigation clavier ---
    input.addEventListener("keydown", (e) => {
        const items = Array.from(document.querySelectorAll(".middo-search-result-item"));

        if (items.length === 0) return;

        if (e.key === "ArrowDown") {
            e.preventDefault();
            currentIndex = (currentIndex + 1) % items.length;
            highlight(items);
        }

        if (e.key === "ArrowUp") {
            e.preventDefault();
            currentIndex = (currentIndex - 1 + items.length) % items.length;
            highlight(items);
        }

        if (e.key === "Escape") {
            resultsBox.style.display = "none";
            input.value = "";
        }

        if (e.key === "Enter" && currentIndex >= 0) {
            items[currentIndex].click();
        }
    });

    function highlight(items) {
        items.forEach(i => i.style.background = "");
        items[currentIndex].style.background = "#e8ecf3";
    }

    // --- Entrée : pipeline complet ---
    input.addEventListener("keydown", async (e) => {
        if (e.key !== "Enter") return;

        const query = input.value.trim();
        if (!query) return;

        addToHistory(query);

        const ctx = createEmptyContext();

        // Pipeline multi-étapes
        const hasPipeline = await runPipeline(query, ctx);
        if (hasPipeline) {
            input.value = "";
            return;
        }

        // Fallback : IA simple sur toute la requête
        await runSingleIA(query, ctx);
        input.value = "";
    });

    // --- Implémentations actions système ---
    function clearNotifications(ctx) {
        alert("Notifications vidées (placeholder)");
        ctx.lastAction = "clear_notifications";
    }

    function closeAllWindows(ctx) {
        document.querySelectorAll(".window").forEach(w => w.remove());
        ctx.lastAction = "close_all_windows";
    }

    function enableDarkMode(ctx) {
        document.body.classList.add("dark-mode");
        ctx.lastAction = "enable_dark_mode";
    }

    function disableDarkMode(ctx) {
        document.body.classList.remove("dark-mode");
        ctx.lastAction = "disable_dark_mode";
    }

    function enableWidgets(ctx) {
        document.querySelectorAll(".widget").forEach(w => w.style.display = "block");
        ctx.lastAction = "enable_widgets";
    }

    function disableWidgets(ctx) {
        document.querySelectorAll(".widget").forEach(w => w.style.display = "none");
        ctx.lastAction = "disable_widgets";
    }

    function openWeatherWidget(ctx) {
        alert("Widget météo (placeholder)");
        ctx.lastAction = "open_weather_widget";
    }

    // --- Actions rapides simples ---
    function openQuickNote() {
        alert("Nouvelle note (placeholder)");
    }

    function openQuickMessage() {
        alert("Nouveau message (placeholder)");
    }

    async function quickSummarize(text) {
        if (!text) return null;

        const res = await fetch("/api/ia", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ prompt: "Résume : " + text })
        });

        const data = await res.json();
        const answer = data.answer || "";
        alert("Résumé :\n\n" + answer);
        return answer;
    }

    async function quickTranslate(text) {
        if (!text) return null;

        const res = await fetch("/api/ia", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ prompt: "Traduis en français : " + text })
        });

        const data = await res.json();
        const answer = data.answer || "";
        alert("Traduction :\n\n" + answer);
        return answer;
    }

    async function quickTranslateTo(lang, text) {
        if (!text) return null;

        const res = await fetch("/api/ia", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ prompt: `Traduis en ${lang} : ` + text })
        });

        const data = await res.json();
        const answer = data.answer || "";
        alert(`Traduction (${lang}) :\n\n` + answer);
        return answer;
    }

    // --- Workflows IA concrets ---
    async function createNoteWorkflow(ctx) {
        alert("Workflow : création de note\n\n" + (ctx.note || ctx.output || ""));
    }

    async function createMessageWorkflow(ctx) {
        alert("Workflow : nouveau message\n\n" + (ctx.message || ctx.output || ""));
    }

});
