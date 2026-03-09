import { create } from 'zustand';

export const useLoadingStore = create((set) => ({
  loading: {},
  start: (key) =>
    set((state) => ({
      loading: { ...state.loading, [key]: true },
    })),
  stop: (key) =>
    set((state) => ({
      loading: { ...state.loading, [key]: false },
    })),
}));
