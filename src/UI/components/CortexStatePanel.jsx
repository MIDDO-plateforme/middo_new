import React from 'react';
import { useCortexStore } from '../store/cortexStore';

export default React.memo(function CortexStatePanel() {
  const snapshot = useCortexStore((s) => s.cortexSnapshot);
  const cortexState = snapshot?.cortex_state || {};

  return (
    <div>
      <h2 className="text-2xl font-semibold mb-4 tracking-tight text-middo-primary dark:text-middo-accent">
        État du Cortex
      </h2>
      <pre className="
        bg-slate-900/60 dark:bg-slate-900/40 
        p-3 rounded-lg max-h-40 overflow-auto text-xs 
        border border-slate-700/40 shadow-inner
      ">
        {snapshot ? JSON.stringify(cortexState, null, 2) : 'Aucun état disponible.'}
      </pre>
    </div>
  );
});
