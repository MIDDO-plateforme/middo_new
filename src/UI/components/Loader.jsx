import React from 'react';

export default function Loader({ size = 32 }) {
  return (
    <div
      className="animate-spin rounded-full border-4 border-slate-500 border-t-middo-primary"
      style={{ width: size, height: size }}
    />
  );
}
