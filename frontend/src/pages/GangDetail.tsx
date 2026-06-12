import { useEffect, useState } from 'react'
import { useParams, Link, useNavigate } from 'react-router-dom'
import { useStore } from '../store'
import FighterCard from '../components/FighterCard'
import AddFighterForm from '../components/AddFighterForm'

export default function GangDetail() {
  const { id } = useParams<{ id: string }>()
  const gangId = Number(id)
  const { currentGang, loading, error, fetchGang, updateGang, deleteGang, createFighter, deleteFighter } = useStore()
  const [showAddFighter, setShowAddFighter] = useState(false)
  const [editingField, setEditingField] = useState<'credits' | 'reputation' | null>(null)
  const [fieldValue, setFieldValue] = useState(0)
  const navigate = useNavigate()

  useEffect(() => { fetchGang(gangId) }, [gangId, fetchGang])

  if (loading) return <div className="text-dark-400 font-mono animate-pulse">Loading gang…</div>
  if (error)   return <div className="text-blood-500">{error}</div>
  if (!currentGang) return null

  const gang = currentGang
  const active  = (gang.fighters ?? []).filter(f => !f.dead)
  const fallen  = (gang.fighters ?? []).filter(f => f.dead)

  const startEdit = (field: 'credits' | 'reputation') => {
    setEditingField(field)
    setFieldValue(gang[field])
  }

  const saveField = async () => {
    if (!editingField) return
    await updateGang(gang.id, { [editingField]: fieldValue })
    setEditingField(null)
  }

  const handleDeleteFighter = async (fighterId: number) => {
    await deleteFighter(fighterId)
  }

  const handleDeleteGang = async () => {
    if (!confirm(`Disband "${gang.name}"? This cannot be undone.`)) return
    await deleteGang(gang.id)
    navigate('/')
  }

  const handleAddFighter = async (data: any) => {
    await createFighter(gang.id, data)
    setShowAddFighter(false)
  }

  return (
    <div>
      <div className="flex items-start justify-between mb-6">
        <div>
          <Link to="/" className="text-xs text-dark-400 hover:text-gold-500 transition-colors mb-1 block">
            ← All Gangs
          </Link>
          <h1 className="font-display text-2xl text-gold-500 tracking-widest uppercase">{gang.name}</h1>
          <div className="text-sm text-dark-300 mt-0.5">{gang.type}</div>
        </div>
        <button
          onClick={handleDeleteGang}
          className="text-xs text-dark-400 hover:text-blood-500 transition-colors border border-dark-700 hover:border-blood-600 rounded px-3 py-1.5"
        >
          Disband Gang
        </button>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-3 gap-4 mb-8">
        {(['credits', 'reputation'] as const).map(field => (
          <div
            key={field}
            className="border border-dark-600 bg-dark-800 rounded p-4 cursor-pointer hover:border-gold-700 transition-colors"
            onClick={() => startEdit(field)}
          >
            <div className="text-xs text-dark-400 uppercase tracking-wider mb-1">
              {field === 'credits' ? '💰 Treasury' : '⚡ Reputation'}
            </div>
            {editingField === field ? (
              <div className="flex gap-2 items-center">
                <input
                  type="number"
                  value={fieldValue}
                  onChange={e => setFieldValue(Number(e.target.value))}
                  autoFocus
                  onBlur={saveField}
                  onKeyDown={e => { if (e.key === 'Enter') saveField(); if (e.key === 'Escape') setEditingField(null) }}
                  className="w-24 bg-dark-700 border border-gold-600 text-dark-100 rounded px-2 py-1 font-mono text-lg focus:outline-none"
                />
              </div>
            ) : (
              <div className="font-mono text-2xl text-gold-400">{gang[field]}</div>
            )}
          </div>
        ))}
        <div className="border border-dark-600 bg-dark-800 rounded p-4">
          <div className="text-xs text-dark-400 uppercase tracking-wider mb-1">👥 Fighters</div>
          <div className="font-mono text-2xl text-gold-400">{active.length}</div>
        </div>
      </div>

      {/* Active fighters */}
      <div className="mb-8">
        <div className="flex items-center justify-between mb-3">
          <h2 className="font-display text-sm text-gold-600 uppercase tracking-widest">Roster</h2>
          <button
            onClick={() => setShowAddFighter(!showAddFighter)}
            className="text-xs px-3 py-1.5 bg-gold-700 hover:bg-gold-600 text-dark-900 font-semibold rounded transition-colors"
          >
            {showAddFighter ? 'Cancel' : '+ Add Fighter'}
          </button>
        </div>

        {showAddFighter && (
          <div className="mb-4">
            <AddFighterForm onSubmit={handleAddFighter} onCancel={() => setShowAddFighter(false)} />
          </div>
        )}

        {active.length === 0 ? (
          <div className="text-dark-400 text-sm text-center py-8 border border-dark-700 rounded">
            No fighters yet. Add your first gang member.
          </div>
        ) : (
          <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            {active.map(f => <FighterCard key={f.id} fighter={f} onDelete={handleDeleteFighter} />)}
          </div>
        )}
      </div>

      {/* Fallen fighters */}
      {fallen.length > 0 && (
        <div>
          <h2 className="font-display text-sm text-blood-500 uppercase tracking-widest mb-3">Fallen</h2>
          <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 opacity-60">
            {fallen.map(f => <FighterCard key={f.id} fighter={f} />)}
          </div>
        </div>
      )}
    </div>
  )
}
