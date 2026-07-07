import { useEffect, useState } from 'react'
import { fighterTemplatesApi } from '../api'
import type { FighterTemplate, GangType } from '../types'

const GANG_TYPES: GangType[] = [
  'Goliath', 'Escher', 'Van Saar', 'Delaque', 'Cawdor', 'Orlock',
  'House of Iron', 'Corpse Grinder Cult', 'Genestealer Cult', 'Enforcers',
]

const STAT_KEYS = ['m','ws','bs','s','t','w','i','a','ld','cl','wil','int_stat'] as const
const STAT_LABELS = ['M', 'WS', 'BS', 'S', 'T', 'W', 'I', 'A', 'Ld', 'Cl', 'Wil', 'Int']

type StatKey = typeof STAT_KEYS[number]

const EMPTY: Omit<FighterTemplate, 'id'> = {
  gang_type: 'Genestealer Cult', name: '', cost: 0,
  m:5, ws:4, bs:4, s:3, t:3, w:1, i:4, a:1, ld:6, cl:7, wil:7, int_stat:7,
  sort_order: 0, notes: '',
}

export default function FighterTemplates() {
  const [templates, setTemplates] = useState<FighterTemplate[]>([])
  const [filterGang, setFilterGang] = useState<string>('Genestealer Cult')
  const [editing, setEditing] = useState<FighterTemplate | null>(null)
  const [creating, setCreating] = useState(false)
  const [form, setForm] = useState<Omit<FighterTemplate, 'id'>>(EMPTY)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => { load() }, [])

  async function load() {
    try {
      const res = await fighterTemplatesApi.list()
      setTemplates(Array.isArray(res.data) ? res.data : [])
    } catch {
      setError('Failed to load templates')
    }
  }

  const filtered = templates.filter(t => t.gang_type === filterGang)

  function startCreate() {
    setForm({ ...EMPTY, gang_type: filterGang })
    setEditing(null)
    setCreating(true)
  }

  function startEdit(t: FighterTemplate) {
    setForm({ ...t })
    setEditing(t)
    setCreating(false)
  }

  function cancel() {
    setCreating(false)
    setEditing(null)
  }

  function setField(key: keyof Omit<FighterTemplate, 'id'>, val: string | number) {
    setForm(f => ({ ...f, [key]: val }))
  }

  async function save() {
    if (!form.name.trim()) return
    setSaving(true)
    setError(null)
    try {
      if (editing) {
        const res = await fighterTemplatesApi.update(editing.id, form)
        setTemplates(ts => ts.map(t => t.id === editing.id ? res.data : t))
      } else {
        const res = await fighterTemplatesApi.create(form)
        setTemplates(ts => [...ts, res.data])
      }
      cancel()
    } catch {
      setError('Failed to save template')
    } finally {
      setSaving(false)
    }
  }

  async function remove(id: number) {
    if (!confirm('Delete this template?')) return
    try {
      await fighterTemplatesApi.delete(id)
      setTemplates(ts => ts.filter(t => t.id !== id))
    } catch {
      setError('Failed to delete template')
    }
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h1 className="font-display text-2xl text-gold-500 tracking-widest uppercase">Fighter Templates</h1>
        <button
          onClick={startCreate}
          className="px-4 py-2 bg-gold-600 hover:bg-gold-500 text-black font-semibold text-sm rounded transition-colors"
        >
          + New Template
        </button>
      </div>

      <div className="mb-6">
        <label className="text-xs text-dark-300 block mb-1">Filter by Faction</label>
        <select
          value={filterGang}
          onChange={e => setFilterGang(e.target.value)}
          className="bg-dark-800 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
        >
          {GANG_TYPES.map(g => <option key={g} value={g}>{g}</option>)}
        </select>
      </div>

      {error && <div className="text-red-400 text-sm mb-4">{error}</div>}

      {(creating || editing) && (
        <div className="border border-gold-800 bg-dark-800 rounded p-4 mb-6 space-y-4">
          <h3 className="font-display text-gold-500 text-sm tracking-wider uppercase">
            {editing ? 'Edit Template' : 'New Template'}
          </h3>
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="text-xs text-dark-300 block mb-1">Faction</label>
              <select
                value={form.gang_type}
                onChange={e => setField('gang_type', e.target.value)}
                className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
              >
                {GANG_TYPES.map(g => <option key={g} value={g}>{g}</option>)}
              </select>
            </div>
            <div>
              <label className="text-xs text-dark-300 block mb-1">Fighter Name / Role</label>
              <input
                value={form.name}
                onChange={e => setField('name', e.target.value)}
                placeholder="e.g. Aberrant"
                className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
              />
            </div>
            <div>
              <label className="text-xs text-dark-300 block mb-1">Cost (credits)</label>
              <input
                type="number" min={0}
                value={form.cost}
                onChange={e => setField('cost', Number(e.target.value))}
                className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
              />
            </div>
            <div>
              <label className="text-xs text-dark-300 block mb-1">Sort Order</label>
              <input
                type="number" min={0}
                value={form.sort_order}
                onChange={e => setField('sort_order', Number(e.target.value))}
                className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
              />
            </div>
          </div>

          <div>
            <div className="text-xs text-dark-400 mb-1">Stats</div>
            <div className="grid grid-cols-12 gap-1">
              {STAT_LABELS.map(l => (
                <div key={l} className="text-center text-xs text-gold-600 font-display">{l}</div>
              ))}
              {STAT_KEYS.map(key => (
                <input
                  key={key}
                  type="number" min={1} max={20}
                  value={form[key as StatKey]}
                  onChange={e => setField(key, Number(e.target.value))}
                  className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-1 py-1 text-xs text-center focus:outline-none focus:border-gold-600 font-mono"
                />
              ))}
            </div>
          </div>

          <div>
            <label className="text-xs text-dark-300 block mb-1">Notes</label>
            <input
              value={form.notes}
              onChange={e => setField('notes', e.target.value)}
              placeholder="Special rules, equipment notes…"
              className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
            />
          </div>

          <div className="flex gap-2 justify-end">
            <button onClick={cancel} className="px-4 py-1.5 text-sm text-dark-300 hover:text-dark-100 transition-colors">
              Cancel
            </button>
            <button
              onClick={save}
              disabled={saving || !form.name.trim()}
              className="px-4 py-1.5 text-sm bg-gold-600 hover:bg-gold-500 text-black font-semibold rounded transition-colors disabled:opacity-50"
            >
              {saving ? 'Saving…' : 'Save Template'}
            </button>
          </div>
        </div>
      )}

      {filtered.length === 0 ? (
        <div className="text-dark-400 text-sm text-center py-16 border border-dark-700 rounded">
          No templates for {filterGang} yet. Click "+ New Template" to add one.
        </div>
      ) : (
        <div className="space-y-2">
          {filtered.map(t => (
            <div key={t.id} className="border border-dark-600 bg-dark-800 rounded p-4">
              <div className="flex items-start justify-between">
                <div>
                  <span className="font-display text-gold-400">{t.name}</span>
                  <span className="ml-3 text-xs font-mono text-dark-300">💰 {t.cost} credits</span>
                  {t.notes && <p className="text-xs text-dark-400 mt-0.5">{t.notes}</p>}
                </div>
                <div className="flex gap-2">
                  <button
                    onClick={() => startEdit(t)}
                    className="text-xs text-gold-600 hover:text-gold-400 transition-colors"
                  >
                    Edit
                  </button>
                  <button
                    onClick={() => remove(t.id)}
                    className="text-xs text-red-600 hover:text-red-400 transition-colors"
                  >
                    Delete
                  </button>
                </div>
              </div>
              <div className="mt-2 grid grid-cols-12 gap-1">
                {STAT_LABELS.map(l => (
                  <div key={l} className="text-center text-xs text-gold-700 font-display">{l}</div>
                ))}
                {STAT_KEYS.map(key => (
                  <div key={key} className="text-center text-xs font-mono text-dark-200">{t[key as StatKey]}{key === 'm' ? '"' : ['ws','bs','i','ld','cl','wil','int_stat'].includes(key) ? '+' : ''}</div>
                ))}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  )
}
