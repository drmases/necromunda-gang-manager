import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useStore } from '../store'
import type { GangType } from '../types'
import { GANG_DESCRIPTIONS } from '../types'

const GANG_TYPES: GangType[] = [
  'Goliath', 'Escher', 'Van Saar', 'Delaque', 'Cawdor', 'Orlock',
  'House of Iron', 'Corpse Grinder Cult', 'Genestealer Cult', 'Enforcers',
]

export default function NewGang() {
  const [name, setName] = useState('')
  const [type, setType] = useState<GangType>('Goliath')
  const [credits, setCredits] = useState(1000)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const { createGang } = useStore()
  const navigate = useNavigate()

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!name.trim()) { setError('Gang name is required'); return }
    setSaving(true)
    setError(null)
    try {
      const gang = await createGang({ name: name.trim(), type, credits, reputation: 0 })
      navigate(`/gangs/${gang.id}`)
    } catch {
      setError('Failed to create gang. Check API connection.')
      setSaving(false)
    }
  }

  return (
    <div className="max-w-xl mx-auto">
      <h1 className="font-display text-2xl text-gold-500 tracking-widest uppercase mb-6">Found a New Gang</h1>

      {error && <div className="text-blood-500 text-sm mb-4 border border-blood-600 rounded px-3 py-2">{error}</div>}

      <form onSubmit={handleSubmit} className="space-y-5">
        <div>
          <label className="text-xs text-dark-300 uppercase tracking-wider block mb-1.5">Gang Name</label>
          <input
            value={name}
            onChange={e => setName(e.target.value)}
            required
            placeholder="Enter gang name"
            className="w-full bg-dark-800 border border-dark-600 text-dark-100 rounded px-4 py-2.5 focus:outline-none focus:border-gold-600 transition-colors"
          />
        </div>

        <div>
          <label className="text-xs text-dark-300 uppercase tracking-wider block mb-1.5">House / Faction</label>
          <div className="grid grid-cols-2 gap-2">
            {GANG_TYPES.map(t => (
              <button
                key={t}
                type="button"
                onClick={() => setType(t)}
                className={`px-3 py-2 rounded border text-sm text-left transition-all ${
                  type === t
                    ? 'border-gold-600 bg-gold-900/30 text-gold-400'
                    : 'border-dark-600 bg-dark-800 text-dark-300 hover:border-dark-500'
                }`}
              >
                {t}
              </button>
            ))}
          </div>
          {type && (
            <div className="mt-2 text-xs text-dark-400 italic border-l-2 border-gold-800 pl-3">
              {GANG_DESCRIPTIONS[type]}
            </div>
          )}
        </div>

        <div>
          <label className="text-xs text-dark-300 uppercase tracking-wider block mb-1.5">Starting Credits</label>
          <input
            type="number"
            min={0}
            value={credits}
            onChange={e => setCredits(Number(e.target.value))}
            className="w-full bg-dark-800 border border-dark-600 text-dark-100 rounded px-4 py-2.5 focus:outline-none focus:border-gold-600 transition-colors font-mono"
          />
        </div>

        <div className="flex gap-3 pt-2">
          <button
            type="button"
            onClick={() => navigate('/')}
            className="px-5 py-2.5 text-sm text-dark-300 hover:text-dark-100 transition-colors border border-dark-700 rounded"
          >
            Cancel
          </button>
          <button
            type="submit"
            disabled={saving}
            className="flex-1 px-5 py-2.5 text-sm bg-gold-600 hover:bg-gold-500 text-dark-900 font-bold rounded transition-colors disabled:opacity-50 font-display tracking-wider"
          >
            {saving ? 'Founding…' : 'Found Gang'}
          </button>
        </div>
      </form>
    </div>
  )
}
