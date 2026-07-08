import { useEffect, useState } from 'react'
import { useNavigate, useSearchParams } from 'react-router-dom'
import { useAuth } from '../AuthContext'

export default function Login() {
  const { authed, login } = useAuth()
  const navigate = useNavigate()
  const [searchParams] = useSearchParams()
  const needsAuth = searchParams.get('reason') === 'auth'
  const [password, setPassword] = useState('')
  const [error, setError] = useState(false)
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    if (authed) navigate('/')
  }, [authed, navigate])

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setSubmitting(true)
    setError(false)
    const ok = await login(password)
    setSubmitting(false)
    if (!ok) setError(true)
  }

  return (
    <div className="max-w-sm mx-auto mt-16">
      <h1 className="font-display text-2xl text-gold-500 tracking-widest uppercase mb-6 text-center">Logga in</h1>
      {needsAuth && (
        <div className="text-gold-500 text-sm text-center mb-4">Du måste logga in för att göra det här.</div>
      )}
      <form onSubmit={handleSubmit} className="border border-gold-800 bg-dark-800 rounded p-4 space-y-4">
        <div>
          <label className="text-xs text-dark-300 block mb-1">Lösenord</label>
          <input
            type="password"
            autoFocus
            value={password}
            onChange={e => setPassword(e.target.value)}
            className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
          />
        </div>
        {error && <div className="text-blood-500 text-sm">Fel lösenord.</div>}
        <button
          type="submit"
          disabled={submitting}
          className="w-full px-4 py-1.5 text-sm bg-gold-600 hover:bg-gold-500 text-black font-semibold rounded transition-colors disabled:opacity-50"
        >
          {submitting ? 'Loggar in…' : 'Logga in'}
        </button>
      </form>
    </div>
  )
}
