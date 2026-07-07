import { useEffect, useState, useRef } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useStore } from '../store'
import type { Gang } from '../types'

export default function GangList() {
  const { gangs, loading, error, fetchGangs, deleteGang } = useStore()
  const [search, setSearch] = useState('')
  const [confirmDelete, setConfirmDelete] = useState<number | null>(null)
  const clickTimer = useRef<ReturnType<typeof setTimeout> | null>(null)
  const navigate = useNavigate()

  useEffect(() => { fetchGangs() }, [fetchGangs])

  const filtered = gangs.filter(g =>
    g.name.toLowerCase().includes(search.toLowerCase()) ||
    g.type.toLowerCase().includes(search.toLowerCase())
  )

  const handleClick = (gang: Gang) => {
    if (confirmDelete === gang.id) {
      if (clickTimer.current) clearTimeout(clickTimer.current)
      deleteGang(gang.id)
      setConfirmDelete(null)
    } else {
      navigate(`/gangs/${gang.id}`)
    }
  }

  const handleDoubleClick = (e: React.MouseEvent, id: number) => {
    e.preventDefault()
    setConfirmDelete(id)
    if (clickTimer.current) clearTimeout(clickTimer.current)
    clickTimer.current = setTimeout(() => setConfirmDelete(null), 3000)
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h1 className="font-display text-2xl text-gold-500 tracking-widest uppercase">Your Gangs</h1>
        <Link
          to="/gangs/new"
          className="px-4 py-2 bg-gold-600 hover:bg-gold-500 text-black font-semibold text-sm rounded transition-colors"
        >
          + New Gang
        </Link>
      </div>

      <input
        value={search}
        onChange={e => setSearch(e.target.value)}
        placeholder="Search gangs…"
        className="mb-6 w-full max-w-sm bg-dark-800 border border-dark-600 text-dark-100 rounded px-4 py-2 text-sm focus:outline-none focus:border-gold-600"
      />

      {error && <div className="text-blood-500 text-sm mb-4">{error}</div>}

      {loading ? (
        <div className="text-dark-400 font-mono text-sm animate-pulse">Loading gangs…</div>
      ) : filtered.length === 0 ? (
        <div className="text-dark-400 text-sm text-center py-16 border border-dark-700 rounded">
          {search ? 'No gangs match your search.' : 'No gangs yet. Create your first gang to begin.'}
        </div>
      ) : (
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {filtered.map(gang => (
            <div
              key={gang.id}
              onClick={() => handleClick(gang)}
              onDoubleClick={e => handleDoubleClick(e, gang.id)}
              className={`border rounded p-4 cursor-pointer transition-all select-none ${
                confirmDelete === gang.id
                  ? 'border-blood-500 bg-blood-600/10 shadow-lg shadow-blood-600/20'
                  : 'border-dark-600 bg-dark-800 hover:border-gold-700'
              }`}
            >
              <div className="flex justify-between items-start">
                <div>
                  <div className="font-display text-base text-gold-400">{gang.name}</div>
                  <div className="text-xs text-dark-300 mt-0.5">{gang.type}</div>
                </div>
                {confirmDelete === gang.id && (
                  <span className="text-xs text-blood-500 font-mono animate-pulse">Click to confirm delete</span>
                )}
              </div>
              <div className="mt-3 grid grid-cols-3 gap-2 text-xs font-mono text-dark-300 border-t border-dark-700 pt-2">
                <span title="Credits">💰 {gang.credits}</span>
                <span title="Reputation">⚡ {gang.reputation}</span>
                <span title="Created">{new Date(gang.created_at).toLocaleDateString()}</span>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  )
}
