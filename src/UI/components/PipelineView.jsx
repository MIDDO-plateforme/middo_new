import React from 'react';
import { useCortexStore } from '../store/cortexStore';

export default React.memo(function PipelineView() {
  const snapshot = useCortexStore((s) => s.cortexSnapshot);
  const pipeline = snapshot?.pipeline || {};

  const steps = [
    { key: 'ingestion', label: 'Ingestion' },
    { key: 'organisation', label: 'Organisation' },
    { key: 'analyse', label: 'Analyse' },
    { key: 'supervision', label: 'Supervision' },
  ];

  return (
    <div>
      <h2 className="text-2xl font-semibold mb-4 tracking-tight text-middo-primary dark:text-middo-accent">
        Pipeline du Cortex
      </h2>
      <div className="flex flex-col gap-3">
        {steps.map((step, index) => {
          const data = pipeline[step.key] || {};
          const active = data.active;
          return (
            <div
              key={step.key}
              className={`
                flex items-center justify-between 
                rounded-xl px-4 py-3 
                transition-all duration-300
                ${active
                  ? 'bg-middo-primary text-white shadow-lg'
                  : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300'
                }
              `}
              style={{ animationDelay: `${index * 0.1}s` }}
            >
              <span className="font-medium">{step.label}</span>
              <span className="text-xs opacity-80">
                {active ? 'Actif' : 'En attente'}
              </span>
            </div>
          );
        })}
      </div>
    </div>
  );
});
