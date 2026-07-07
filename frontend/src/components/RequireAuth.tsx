import { useAuth } from '../AuthContext'
import Login from '../pages/Login'

export default function RequireAuth({ children }: { children: React.ReactNode }) {
  const { authed, checking } = useAuth()

  if (checking) return <div className="text-dark-400 font-mono animate-pulse">Laddar…</div>
  if (!authed) return <Login />
  return <>{children}</>
}
