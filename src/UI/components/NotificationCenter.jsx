import React, { useEffect } from 'react';
import { useNotificationStore } from '../store/notificationStore';

export default function NotificationCenter() {
  const notifications = useNotificationStore((s) => s.notifications);
  const remove = useNotificationStore((s) => s.remove);

  useEffect(() => {
    const timers = notifications.map((n) =>
      setTimeout(() => remove(n.id), 4000)
    );
    return () => timers.forEach(clearTimeout);
  }, [notifications, remove]);

  return (
    <div className="fixed top-4 right-4 z-50 space-y-3">
      {notifications.map((n) => (
        <div
          key={n.id}
          className={`px-4 py-3 rounded-lg shadow-lg text-white animate-slideUp ${
            n.type === 'success'
              ? 'bg-green-600'
              : n.type === 'error'
              ? 'bg-red-600'
              : 'bg-blue-600'
          }`}
        >
          {n.message}
        </div>
      ))}
    </div>
  );
}
