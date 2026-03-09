import React, { useEffect, useState } from 'react';
import Loader from './Loader';
import { useLoadingStore } from '../store/loadingStore';
import { useNotificationStore } from '../store/notificationStore';

export default React.memo(function MemoryPanel() {
  const [memory, setMemory] = useState(null);
  const loading = useLoadingStore((s) => s.loading);
  const start = useLoadingStore((s) => s.start);
  const stop = useLoadingStore((s) => s.stop);
  const success = useNotificationStore((s) => s.success);
  const error = useNotificationStore((s) => s.error);

  const loadMemory = async () => {
    start('memory');
    try {
      const res = await fetch('/api/memory/all');
      if (!res.ok) throw new Error('Erreur chargement mémoire');
      const data = await res.json();
      setMemory(data.memory || null);
      success('Mémoire chargée');
    } catch (e) {
      error(e.message || 'Erreur inconnue');
    } finally {
      stop('memory');
    }
  };

  useEffect(() => {
    loadMemory();
  }, []);

  return (
    <div className="mt-4">
      <h3 className="text-lg font-medium mb-2 text-slate-600 dark:text-slate-300">
        Mémoire du Cortex
      </h3>
      {loading['memory'] ? (
        <div className="flex justify-center py-4">
          <Loader size={28} />
        </div>
      ) : (
        <pre className="
          bg-slate-900/60 dark:bg-slate-900/40 
          p-3 rounded-lg max-h-40 overflow-auto text-xs 
          border border-slate-700/40 shadow-inner
        ">
          {memory ? JSON.stringify(memory, null, 2) : 'Aucune mémoire chargée.'}
        </pre>
      )}
    </div>
  );
});
