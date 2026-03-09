import React, { useState } from 'react';
import Loader from './Loader';
import { useLoadingStore } from '../store/loadingStore';
import { useNotificationStore } from '../store/notificationStore';

export default React.memo(function ActionsPanel() {
  const [plan, setPlan] = useState(null);
  const [safety, setSafety] = useState(null);
  const [execution, setExecution] = useState(null);

  const loading = useLoadingStore((s) => s.loading);
  const start = useLoadingStore((s) => s.start);
  const stop = useLoadingStore((s) => s.stop);
  const success = useNotificationStore((s) => s.success);
  const error = useNotificationStore((s) => s.error);

  const generatePlan = async () => {
    start('actions-plan');
    try {
      const res = await fetch('/api/actions/plan');
      if (!res.ok) throw new Error('Erreur planification');
      const data = await res.json();
      setPlan(data.plan || null);
      success('Plan généré');
    } catch (e) {
      error(e.message || 'Erreur inconnue');
    } finally {
      stop('actions-plan');
    }
  };

  const checkSafety = async () => {
    start('actions-safety');
    try {
      const res = await fetch('/api/actions/safety');
      if (!res.ok) throw new Error('Erreur vérification sécurité');
      const data = await res.json();
      setSafety(data.safety || null);
      success('Sécurité vérifiée');
    } catch (e) {
      error(e.message || 'Erreur inconnue');
    } finally {
      stop('actions-safety');
    }
  };

  const executePlan = async () => {
    start('actions-exec');
    try {
      const res = await fetch('/api/actions/execute');
      if (!res.ok) throw new Error('Erreur exécution');
      const data = await res.json();
      setExecution(data.execution || null);
      success('Plan exécuté');
    } catch (e) {
      error(e.message || 'Erreur inconnue');
    } finally {
      stop('actions-exec');
    }
  };

  return (
    <div>
      <h2 className="text-2xl font-semibold mb-4 tracking-tight text-middo-primary dark:text-middo-accent">
        Actions IA
      </h2>

      <div className="flex flex-wrap gap-3 mb-4">
        <button
          onClick={generatePlan}
          className="
            px-4 py-2 rounded-lg 
            bg-middo-primary hover:bg-middo-secondary 
            text-white font-medium 
            transition-all duration-300 
            hover:scale-[1.03] active:scale-[0.97]
            shadow-md hover:shadow-lg
          "
        >
          {loading['actions-plan'] ? 'Planification…' : 'Générer un plan'}
        </button>
        <button
          onClick={checkSafety}
          className="
            px-4 py-2 rounded-lg 
            bg-emerald-600 hover:bg-emerald-700 
            text-white font-medium 
            transition-all duration-300 
            hover:scale-[1.03] active:scale-[0.97]
            shadow-md hover:shadow-lg
          "
        >
          {loading['actions-safety'] ? 'Vérification…' : 'Vérifier la sécurité'}
        </button>
        <button
          onClick={executePlan}
          className="
            px-4 py-2 rounded-lg 
            bg-amber-600 hover:bg-amber-700 
            text-white font-medium 
            transition-all duration-300 
            hover:scale-[1.03] active:scale-[0.97]
            shadow-md hover:shadow-lg
          "
        >
          {loading['actions-exec'] ? 'Exécution…' : 'Exécuter le plan'}
        </button>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-3 text-xs">
        <pre className="
          bg-slate-900/60 dark:bg-slate-900/40 
          p-3 rounded-lg max-h-40 overflow-auto 
          border border-slate-700/40 shadow-inner
        ">
          {plan ? JSON.stringify(plan, null, 2) : 'Aucun plan.'}
        </pre>
        <pre className="
          bg-slate-900/60 dark:bg-slate-900/40 
          p-3 rounded-lg max-h-40 overflow-auto 
          border border-slate-700/40 shadow-inner
        ">
          {safety ? JSON.stringify(safety, null, 2) : 'Aucune vérification.'}
        </pre>
        <pre className="
          bg-slate-900/60 dark:bg-slate-900/40 
          p-3 rounded-lg max-h-40 overflow-auto 
          border border-slate-700/40 shadow-inner
        ">
          {execution ? JSON.stringify(execution, null, 2) : 'Aucune exécution.'}
        </pre>
      </div>
    </div>
  );
});
