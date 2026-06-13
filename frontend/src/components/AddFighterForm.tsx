import { useEffect, useState } from 'react'
import type { FighterStats, FighterTemplate } from '../types'
import { DEFAULT_STATS } from '../types'
import { fighterTemplatesApi } from '../api'

const STAT_KEYS: (keyof FighterStats)[] = ['m', 'ws', 'bs', 's', 't', 'w', 'i', 'a', 'ld', 'cl', 'wil', 'int']
const STAT_LABELS = ['M', 'WS', 'BS', 'S', 'T', 'W', 'I', 'A', 'Ld', 'Cl', 'Wil', 'Int']

interface Props {
  gangType: string
  onSubmit: (data: any) => Promise<void>
  onCancel: () => void
}

export default function AddFighterForm({ gangType, onSubmit, onCancel }: Props) {
  const [templates, setTemplates] = useState<FighterTemplate[]>([])
  const [name, setName] = useState('')
  const [fighterRole, setFighterRole] = useState('')
  const [cost, setCost] = useState(50)
  const [stats, setStats] = useState<FighterStats>(DEFAULT_STATS['Ganger'])
  const [saving, setSaving] = useState(false)
  const [templateSpecialRules, setTemplateSpecialRules] = useState<{name:string,description:string}[]>([])

  useEffect(() => {
    fighterTemplatesApi.list(gangType).then(res => {
      setTemplates(Array.isArray(res.data) ? res.data : [])
    }).catch(() => setTemplates([]))
  }, [gangType])

  function applyTemplate(t: FighterTemplate) {
    setFighterRole(t.name)
    setCost(Number(t.cost))
    try {
      setTemplateSpecialRules(t.special_rules ? JSON.parse(t.special_rules) : [])
    } catch { setTemplateSpecialRules([]) }
    setStats({
      m:   Number(t.m),
      ws:  Number(t.ws),
      bs:  Number(t.bs),
      s:   Number(t.s),
      t:   Number(t.t),
      w:   Number(t.w),
      i:   Number(t.i),
      a:   Number(t.a),
      ld:  Number(t.ld),
      cl:  Number(t.cl),
      wil: Number(t.wil),
      int: Number(t.int_stat),
    })
  }

  const handleStatChange = (key: keyof FighterStats, val: number) => {
    setStats(s => ({ ...s, [key]: val }))
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!name.trim()) return
    setSaving(true)
    try {
      await onSubmit({ name: name.trim(), type: fighterRole || 'Ganger', cost, ...stats, special_rules: templateSpecialRules })
    } finally {
      setSaving(false)
    }
  }

  return (
    <form onSubmit={handleSubmit} className="border border-gold-800 bg-dark-800 rounded p-4 space-y-4">
      <h3 className="font-display text-gold-500 text-sm tracking-wider uppercase">Add Fighter</h3>

      {templates.length > 0 && (
        <div>
          <label className="text-xs text-dark-300 block mb-1">
            Pick role from {gangType} roster
          </label>
          <select
            value={fighterRole}
            onChange={e => {
              const t = templates.find(t => t.name === e.target.value)
              if (t) applyTemplate(t)
            }}
            className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
          >
            <option value="">— Select fighter type —</option>
            {templates.map(t => (
              <option key={t.id} value={t.name}>{t.name} ({t.cost}¢)</option>
            ))}
          </select>
        </div>
      )}

      <div className="grid grid-cols-2 gap-3">
        <div>
          <label className="text-xs text-dark-300 block mb-1">Fighter Name</label>
          <input
            value={name}
            onChange={e => setName(e.target.value)}
            required
            className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
            placeholder="e.g. Brother Krix"
          />
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

      <div>
        <div className="text-xs text-dark-400 mb-1">Stats</div>
        <div className="overflow-x-auto">
        <div className="grid grid-cols-12 gap-1" style={{minWidth: '480px'}}>
          {STAT_LABELS.map(l => (
            <div key={l} className="text-center text-xs text-gold-600 font-display">{l}</div>
          ))}
          {STAT_KEYS.map(key => {
            const suffix = key === 'm' ? '"' : ['ws','bs','i','ld','cl','wil','int'].includes(key) ? '+' : ''
            return (
              <div key={key} className="flex items-center justify-center gap-0.5">
                <input
                  type="number"
                  min={1}
                  max={20}
                  value={stats[key]}
                  onChange={e => handleStatChange(key, Number(e.target.value))}
                  className="w-8 bg-dark-700 border border-dark-600 text-dark-100 rounded px-1 py-1 text-xs text-center focus:outline-none focus:border-gold-600 font-mono"
                />
                {suffix && <span className="text-xs text-dark-400">{suffix}</span>}
              </div>
            )
          })}
        </div>
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
