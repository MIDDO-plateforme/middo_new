import { create } from 'zustand';

export const useCortexStore = create((set) => ({
  cortexSnapshot: null,
  setSnapshot: (snapshot) => set({ cortexSnapshot: snapshot }),
}));
