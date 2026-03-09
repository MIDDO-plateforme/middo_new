import React from 'react';
import { useCortexStore } from '../store/cortexStore';

export default React.memo(function CortexMiniMap() {
  const snapshot = useCortexStore((s) => s.cortexSnapshot);

  const pipeline = snapshot?.pipeline || {};
  const cortexState = snapshot?.cortex_state || {};
  const lastFlux = snapshot?.last_flux || null;

  const steps = [
    { key: 'ingestion', label: 'Ingestion' },
    { key: 'organisation', label: 'Organisation' },
    { key: 'analyse', label: 'Analyse' },
    { key: 'supervision', label: 'Supervision' },
  ];

  return (
    <div className="
      bg-white dark:bg-slate-800 
      rounded-2xl p-6 shadow-xl 
      border border-slate-200 dark:border-slate-700 
      backdrop-blur-sm 
      transition-all duration-400 
      hover:shadow-2xl hover:-translate-y-1
      animate-fadeIn
    ">
      <h2 className="text-2xl font-semibold mb-4 tracking-tight text-middo-primary dark:text-middo-accent">
        Mini‑map cognitive du Cortex
      </h2>

      <div className="grid grid-cols-4 gap-4 mb-6">
        {steps.map((step, i) => {
          const active = pipeline?.[step.key]?.active;
          return (
            <div
              key={step.key}
              className={`
                p-3 rounded-xl text-center text-sm font-medium 
                transition-all duration-300
                ${active
                  ? 'bg-middo-primary text-white shadow-lg scale-[1.05]'
                  : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300'
                }
              `}
              style={{ animationDelay: `${i * 0.1}s` }}
            >
              {step.label}
            </div>
          );
        })}
      </div>

      <h3 className="text-lg font-medium mb-2 text-slate-600 dark:text-slate-300">
        Agents actifs
      </h3>
      <div className="flex gap-3 mb-6">
        {['planner', 'safety', 'executor'].map((agent) => {
          const active = cortexState?.agents?.[agent]?.active;
          return (
            <div
              key={agent}
              className={`
                px-3 py-2 rounded-lg text-sm font-medium 
                transition-all duration-300
                ${active
                  ? 'bg-green-600 text-white shadow-md scale-[1.05]'
                  : 'bg-slate-300 dark:bg-slate-700 text-slate-600 dark:text-slate-300'
                }
              `}
            >
              {agent}
            </div>
          );
        })}
      </div>

      <h3 className="text-lg font-medium mb-2 text-slate-600 dark:text-slate-300">
        Mémoire
      </h3>
      <div className="grid grid-cols-3 gap-3 mb-6">
        {['declarative', 'vector', 'procedural'].map((mem) => (
          <div
            key={mem}
            className="
              bg-slate-200 dark:bg-slate-700 
              text-slate-700 dark:text-slate-300 
              p-3 rounded-xl text-center text-sm 
              shadow-inner
            "
          >
            {mem}
          </div>
        ))}
      </div>

      <h3 className="text-lg font-medium mb-2 text-slate-600 dark:text-slate-300">
        Dernier flux
      </h3>
      <pre className="
        bg-slate-900/60 dark:bg-slate-900/40 
        p-3 rounded-lg max-h-32 overflow-auto text-xs 
        border border-slate-700/40 shadow-inner
      ">
        {lastFlux ? JSON.stringify(lastFlux, null, 2) : 'Aucun flux.'}
      </pre>
    </div>
  );
});
