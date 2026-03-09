import { create } from 'zustand';

export const useNotificationStore = create((set) => ({
  notifications: [],

  push: (type, message) =>
    set((state) => ({
      notifications: [
        ...state.notifications,
        { id: Date.now(), type, message },
      ],
    })),

  success: (message) =>
    set((state) => ({
      notifications: [
        ...state.notifications,
        { id: Date.now(), type: 'success', message },
      ],
    })),

  error: (message) =>
    set((state) => ({
      notifications: [
        ...state.notifications,
        { id: Date.now(), type: 'error', message },
      ],
    })),

  info: (message) =>
    set((state) => ({
      notifications: [
        ...state.notifications,
        { id: Date.now(), type: 'info', message },
      ],
    })),

  remove: (id) =>
    set((state) => ({
      notifications: state.notifications.filter((n) => n.id !== id),
    })),
}));
