import { createContext, useContext, useEffect, useState } from 'react'
import { authApi } from './api'

interface AuthState {
  authed: boolean
  checking: boolean
  login: (password: string) => Promise<boolean>
  logout: () => Promise<void>
}

const AuthContext = createContext<AuthState | null>(null)

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [authed, setAuthed] = useState(false)
  const [checking, setChecking] = useState(true)

  useEffect(() => {
    authApi.status()
      .then(res => setAuthed(res.data.authed))
      .catch(() => setAuthed(false))
      .finally(() => setChecking(false))
  }, [])

  const login = async (password: string) => {
    try {
      await authApi.login(password)
      setAuthed(true)
      return true
    } catch {
      return false
    }
  }

  const logout = async () => {
    await authApi.logout().catch(() => {})
    setAuthed(false)
  }

  return (
    <AuthContext.Provider value={{ authed, checking, login, logout }}>
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth(): AuthState {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth must be used within AuthProvider')
  return ctx
}
