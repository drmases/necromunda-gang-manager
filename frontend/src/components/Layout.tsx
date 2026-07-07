import { Link, useLocation } from 'react-router-dom'
import { useAuth } from '../AuthContext'

export default function Layout({ children }: { children: React.ReactNode }) {
  const location = useLocation()
  const { authed, logout } = useAuth()
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
            <Link
              to="/templates"
              className={`hover:text-gold-400 transition-colors ${location.pathname === '/templates' ? 'text-gold-500' : 'text-dark-300'}`}
            >
              Fighters
            </Link>
            <Link
              to="/weapons"
              className={`hover:text-gold-400 transition-colors ${location.pathname === '/weapons' ? 'text-gold-500' : 'text-dark-300'}`}
            >
              Equipment
            </Link>
            <Link
              to="/skills"
              className={`hover:text-gold-400 transition-colors ${location.pathname === '/skills' ? 'text-gold-500' : 'text-dark-300'}`}
            >
              Skills
            </Link>
            <Link
              to="/injuries"
              className={`hover:text-gold-400 transition-colors ${location.pathname === '/injuries' ? 'text-gold-500' : 'text-dark-300'}`}
            >
              Injuries
            </Link>
            {authed ? (
              <button onClick={logout} className="text-dark-300 hover:text-blood-500 transition-colors">Logga ut</button>
            ) : (
              <Link
                to="/login"
                className={`hover:text-gold-400 transition-colors ${location.pathname === '/login' ? 'text-gold-500' : 'text-dark-300'}`}
              >
                Logga in
              </Link>
            )}
          </nav>
        </div>
      </header>
      <main className="max-w-6xl mx-auto px-4 py-6">{children}</main>
    </div>
  )
}
