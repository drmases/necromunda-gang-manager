import { Link, useLocation } from 'react-router-dom'

export default function Layout({ children }: { children: React.ReactNode }) {
  const location = useLocation()
  return (
    <div className="min-h-screen bg-dark-900 text-dark-100">
      <header className="border-b border-gold-800 bg-dark-800">
        <div className="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
          <Link to="/" className="font-display text-xl text-gold-500 hover:text-gold-400 tracking-widest uppercase">
            ☠ Necromunda Gang Manager
          </Link>
          <nav className="flex gap-4 text-sm">
            <Link
              to="/"
              className={`hover:text-gold-400 transition-colors ${location.pathname === '/' ? 'text-gold-500' : 'text-dark-300'}`}
            >
              Gangs
            </Link>
            <Link
              to="/gangs/new"
              className={`hover:text-gold-400 transition-colors ${location.pathname === '/gangs/new' ? 'text-gold-500' : 'text-dark-300'}`}
            >
              + New Gang
            </Link>
          </nav>
        </div>
      </header>
      <main className="max-w-6xl mx-auto px-4 py-6">{children}</main>
    </div>
  )
}
