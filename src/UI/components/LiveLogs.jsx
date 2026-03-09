import React, { useEffect, useState } from 'react';

export default React.memo(function LiveLogs() {
  const [logs, setLogs] = useState([]);

  useEffect(() => {
    const evtSource = new EventSource('/api/logs/stream');

    evtSource.onmessage = (event) => {
      try {
        const data = JSON.parse(event.data);
        setLogs((prev) => [...prev.slice(-200), data]);
      } catch {
        // ignore parse errors
      }
    };

    evtSource.onerror = () => {
      // optional: could push a notification
    };

    return () => evtSource.close();
  }, []);

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
        Logs en temps réel
      </h2>
      <div className="space-y-1 text-xs font-mono max-h-56 overflow-auto">
        {logs.map((log, i) => (
          <div key={i} className="text-slate-300">
            <span className="text-slate-500 mr-2">{log.time}</span>
            {log.message}
          </div>
        ))}
      </div>
    </div>
  );
});
