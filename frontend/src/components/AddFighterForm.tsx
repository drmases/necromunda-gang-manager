import { useState } from 'react'
import type { FighterType, FighterStats } from '../types'
import { DEFAULT_STATS } from '../types'

const FIGHTER_TYPES: FighterType[] = [
  'Leader', 'Champion', 'Ganger', 'Juve', 'Prospect', 'Crew', 'Exotic Beast', 'Hanger-on',
]
const STAT_KEYS: (keyof FighterStats)[] = ['m', 'ws', 'bs', 's', 't', 'w', 'i', 'a', 'ld', 'cl', 'wil', 'int']
const STAT_LABELS = ['M', 'WS', 'BS', 'S', 'T', 'W', 'I', 'A', 'Ld', 'Cl', 'Wil', 'Int']

interface Props {
  onSubmit: (data: any) => Promise<void>
  onCancel: () => void
}

export default function AddFighterForm({ onSubmit, onCancel }: Props) {
  const [name, setName] = useState('')
  const [type, setType] = useState<FighterType>('Ganger')
  const [cost, setCost] = useState(50)
  const [stats, setStats] = useState<FighterStats>(DEFAULT_STATS['Ganger'])
  const [saving, setSaving] = useState(false)

  const handleTypeChange = (t: FighterType) => {
    setType(t)
    setStats(DEFAULT_STATS[t])
  }

  const handleStatChange = (key: keyof FighterStats, val: number) => {
    setStats(s => ({ ...s, [key]: val }))
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!name.trim()) return
    setSaving(true)
    try {
      await onSubmit({ name: name.trim(), type, cost, ...stats })
    } finally {
      setSaving(false)
    }
  }

  return (
    <form onSubmit={handleSubmit} className="border border-gold-800 bg-dark-800 rounded p-4 space-y-4">
      <h3 className="font-display text-gold-500 text-sm tracking-wider uppercase">Add Fighter</h3>
      <div className="grid grid-cols-2 gap-3">
        <div>
          <label className="text-xs text-dark-300 block mb-1">Name</label>
          <input
            value={name}
            onChange={e => setName(e.target.value)}
            required
            className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
            placeholder="Fighter name"
          />
        </div>
        <div>
          <label className="text-xs text-dark-300 block mb-1">Type</label>
          <select
            value={type}
            onChange={e => handleTypeChange(e.target.value as FighterType)}
            className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
          >
            {FIGHTER_TYPES.map(t => <option key={t} value={t}>{t}</option>)}
          </select>
        </div>
        <div>
          <label className="text-xs text-dark-300 block mb-1">Cost (credits)</label>
          <input
            type="number"
            min={0}
            value={cost}
            onChange={e => setCost(Number(e.target.value))}
            className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
          />
        </div>
      </div>

      <div className="overflow-x-auto">
        <div className="text-xs text-dark-400 mb-1">Stats</div>
        <div className="grid grid-cols-12 gap-1 min-w-max">
          {STAT_LABELS.map(l => (
            <div key={l} className="text-center text-xs text-gold-600 font-display">{l}</div>
          ))}
          {STAT_KEYS.map(key => (
            <input
              key={key}
              type="number"
              min={1}
              max={20}
              value={stats[key]}
              onChange={e => handleStatChange(key, Number(e.target.value))}
              className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-1 py-1 text-xs text-center focus:outline-none focus:border-gold-600 font-mono"
            />
          ))}
        </div>
      </div>

      <div className="flex gap-2 justify-end">
        <button
          type="button"
          onClick={onCancel}
          className="px-4 py-1.5 text-sm text-dark-300 hover:text-dark-100 transition-colors"
        >
          Cancel
        </button>
        <button
          type="submit"
          disabled={saving}
          className="px-4 py-1.5 text-sm bg-gold-600 hover:bg-gold-500 text-dark-900 font-semibold rounded transition-colors disabled:opacity-50"
        >
          {saving ? 'Adding…' : 'Add Fighter'}
        </button>
      </div>
    </form>
  )
}
