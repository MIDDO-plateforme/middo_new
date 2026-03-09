import React from 'react';
import {
  HomeIcon,
  CpuChipIcon,
  BookOpenIcon,
  BoltIcon,
  Cog6ToothIcon,
  SunIcon,
  MoonIcon,
} from '@heroicons/react/24/outline';
import { useThemeStore } from '../store/themeStore';

export default function Sidebar() {
  const theme = useThemeStore((s) => s.theme);
  const toggleTheme = useThemeStore((s) => s.toggleTheme);

  const menu = [
    { label: 'Dashboard', icon: <HomeIcon className="h-6 w-6" />, href: '#' },
    { label: 'Cortex', icon: <CpuChipIcon className="h-6 w-6" />, href: '#' },
    { label: 'Mémoire', icon: <BookOpenIcon className="h-6 w-6" />, href: '#' },
    { label: 'Actions', icon: <BoltIcon className="h-6 w-6" />, href: '#' },
    { label: 'Paramètres', icon: <Cog6ToothIcon className="h-6 w-6" />, href: '#' },
  ];

  return (
    <div className="hidden lg:flex fixed left-0 top-0 h-full w-64 bg-slate-900 border-r border-slate-700 p-6 flex-col animate-slideUp">
      <h1 className="text-2xl font-bold text-white mb-8 tracking-wide">
        MIDDO OS + IA
      </h1>

      <nav className="flex flex-col gap-4">
        {menu.map((item) => (
          <a
            key={item.label}
            href={item.href}
            className="
              flex items-center gap-3 px-3 py-2 
              rounded-lg text-slate-300 
              hover:bg-slate-800 hover:text-white 
              transition-all duration-300
            "
          >
            {item.icon}
            <span className="text-lg">{item.label}</span>
          </a>
        ))}
      </nav>

      <div
        className="
          mt-8 flex items-center justify-between 
          bg-slate-800 p-3 rounded-lg cursor-pointer 
          hover:bg-slate-700 transition-all duration-300 
          animate-pulseSoft
        "
        onClick={toggleTheme}
      >
        <span className="text-slate-300">Thème</span>
        {theme === 'dark' ? (
          <SunIcon className="h-6 w-6 text-yellow-400" />
        ) : (
          <MoonIcon className="h-6 w-6 text-slate-300" />
        )}
      </div>

      <div className="mt-auto pt-6 border-t border-slate-700">
        <p className="text-slate-500 text-sm">
          Cockpit premium
        </p>
      </div>
    </div>
  );
}
