import React, { useState } from 'react';
import Loader from './Loader';
import { useLoadingStore } from '../store/loadingStore';
import { useNotificationStore } from '../store/notificationStore';
import { useCortexStore } from '../store/cortexStore';

export default React.memo(function FluxInput() {
  const [flux, setFlux] = useState('');
  const loading = useLoadingStore((s) => s.loading);
  const start = useLoadingStore((s) => s.start);
  const stop = useLoadingStore((s) => s.stop);
  const success = useNotificationStore((s) => s.success);
  const error = useNotificationStore((s) => s.error);
  const setSnapshot = useCortexStore((s) => s.setSnapshot);

  const sendFlux = async () => {
    if (!flux.trim()) return;
    start('cortex');
    try {
      const res = await fetch('/api/cortex/flux', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ flux }),
      });
      if (!res.ok) throw new Error("Erreur lors de l'envoi au Cortex");
      const data = await res.json();
      setSnapshot(data.snapshot || null);
      success('Flux envoyé au Cortex');
    } catch (e) {
      error(e.message || 'Erreur inconnue');
    } finally {
      stop('cortex');
    }
  };

  return (
    <div>
      <h2 className="text-2xl font-semibold mb-4 tracking-tight text-middo-primary dark:text-middo-accent">
        Flux vers le Cortex
      </h2>
      <textarea
        className="
          w-full h-32 rounded-xl 
          bg-slate-100 dark:bg-slate-900 
          border border-slate-300 dark:border-slate-700 
          p-3 text-sm 
          focus:outline-none focus:ring-2 focus:ring-middo-primary 
          transition-all duration-300
        "
        value={flux}
        onChange={(e) => setFlux(e.target.value)}
        placeholder="Décris la situation, la tâche, le besoin…"
      />
      <div className="mt-3 flex items-center justify-between">
        <span className="text-xs text-slate-500 dark:text-slate-400">
          Le Cortex analysera ce flux et mettra à jour la mini‑map.
        </span>
        {loading['cortex'] ? (
          <Loader size={28} />
        ) : (
          <button
            onClick={sendFlux}
            className="
              px-4 py-2 
              rounded-lg 
              bg-middo-primary 
              hover:bg-middo-secondary 
              text-white 
              font-medium 
              transition-all duration-300 
              hover:scale-[1.03] active:scale-[0.97]
              shadow-md hover:shadow-lg
            "
          >
            Envoyer au Cortex
          </button>
        )}
      </div>
    </div>
  );
});
