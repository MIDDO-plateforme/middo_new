import React, { Suspense, lazy } from 'react';
import Sidebar from './components/Sidebar';
import NotificationCenter from './components/NotificationCenter';
import { useThemeStore } from './store/themeStore';

import FluxInput from './components/FluxInput';
const PipelineView = lazy(() => import('./components/PipelineView'));
const CortexStatePanel = lazy(() => import('./components/CortexStatePanel'));
const MemoryPanel = lazy(() => import('./components/MemoryPanel'));
const ActionsPanel = lazy(() => import('./components/ActionsPanel'));
const CortexMiniMap = lazy(() => import('./components/CortexMiniMap'));
const LiveLogs = lazy(() => import('./components/LiveLogs'));
import Loader from './components/Loader';

export default function Dashboard() {
  const theme = useThemeStore((s) => s.theme);

  return (
    <div className={theme === 'dark' ? 'dark' : ''}>
      <div className="bg-white dark:bg-slate-900 dark:text-white min-h-screen flex">
        <Sidebar />
        <NotificationCenter />

        <div className="flex-1 flex flex-col lg:ml-64">
          <header className="lg:hidden px-4 py-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h1 className="text-lg font-semibold">MIDDO OS + IA</h1>
            <span className="text-xs text-slate-500 dark:text-slate-400">
              Cockpit
            </span>
          </header>

          <main className="p-4 lg:p-6 grid grid-cols-1 gap-4 lg:grid-cols-2 lg:grid-rows-3 lg:gap-6">
            <div className="
              bg-white dark:bg-slate-800 
              rounded-2xl p-6 shadow-xl 
              border border-slate-200 dark:border-slate-700 
              backdrop-blur-sm 
              transition-all duration-400 
              hover:shadow-2xl hover:-translate-y-1
              animate-fadeIn
            ">
              <FluxInput />
            </div>

            <Suspense fallback={<div className="flex justify-center items-center"><Loader size={40} /></div>}>
              <div className="
                bg-white dark:bg-slate-800 
                rounded-2xl p-6 shadow-xl 
                border border-slate-200 dark:border-slate-700 
                backdrop-blur-sm 
                transition-all duration-400 
                hover:shadow-2xl hover:-translate-y-1
                animate-fadeIn
              ">
                <PipelineView />
              </div>
            </Suspense>

            <Suspense fallback={<div className="flex justify-center items-center"><Loader size={40} /></div>}>
              <div className="
                bg-white dark:bg-slate-800 
                rounded-2xl p-6 shadow-xl 
                border border-slate-200 dark:border-slate-700 
                backdrop-blur-sm 
                transition-all duration-400 
                hover:shadow-2xl hover:-translate-y-1
                animate-fadeIn
              ">
                <CortexStatePanel />
                <MemoryPanel />
              </div>
            </Suspense>

            <Suspense fallback={<div className="flex justify-center items-center"><Loader size={40} /></div>}>
              <div className="
                bg-white dark:bg-slate-800 
                rounded-2xl p-6 shadow-xl 
                border border-slate-200 dark:border-slate-700 
                backdrop-blur-sm 
                transition-all duration-400 
                hover:shadow-2xl hover:-translate-y-1
                animate-fadeIn
              ">
                <ActionsPanel />
              </div>
            </Suspense>

            <Suspense fallback={<div className="flex justify-center items-center"><Loader size={40} /></div>}>
              <CortexMiniMap />
            </Suspense>

            <Suspense fallback={<div className="flex justify-center items-center"><Loader size={40} /></div>}>
              <LiveLogs />
            </Suspense>
          </main>
        </div>
      </div>
    </div>
  );
}
