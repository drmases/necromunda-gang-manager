import { useEffect, useState } from 'react'
import { weaponLibraryApi } from '../api'
import type { WeaponLibraryEntry, GangType } from '../types'

const GANG_TYPES: GangType[] = [
  'Goliath', 'Escher', 'Van Saar', 'Delaque', 'Cawdor', 'Orlock',
  'House of Iron', 'Corpse Grinder Cult', 'Genestealer Cult', 'Enforcers',
]

const PROFILE_KEYS = ['range_s','range_l','hit_s','hit_l','str','ap','dmg','ammo'] as const
const PROFILE_LABELS = ['Rng S','Rng L','Hit S','Hit L','Str','AP','Dmg','Ammo']

type ProfileKey = typeof PROFILE_KEYS[number]

const EMPTY: Omit<WeaponLibraryEntry, 'id'> = {
  gang_type: 'Genestealer Cult', name: '', cost: 0,
  range_s: '-', range_l: '-', hit_s: '-', hit_l: '-',
  str: '-', ap: '-', dmg: '1', ammo: '-',
  traits: '', sort_order: 0,
}

export default function WeaponLibrary() {
  const [weapons, setWeapons] = useState<WeaponLibraryEntry[]>([])
  const [filterGang, setFilterGang] = useState<string>('Genestealer Cult')
  const [editing, setEditing] = useState<WeaponLibraryEntry | null>(null)
  const [creating, setCreating] = useState(false)
  const [form, setForm] = useState<Omit<WeaponLibraryEntry, 'id'>>(EMPTY)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => { load() }, [])

  async function load() {
    try {
      const res = await weaponLibraryApi.list()
      setWeapons(Array.isArray(res.data) ? res.data : [])
    } catch {
      setError('Failed to load weapon library')
    }
  }

  const filtered = weapons.filter(w => w.gang_type === filterGang)

  function startCreate() {
    setForm({ ...EMPTY, gang_type: filterGang })
    setEditing(null)
    setCreating(true)
  }

  function startEdit(w: WeaponLibraryEntry) {
    setForm({ ...w })
    setEditing(w)
    setCreating(false)
  }

  function cancel() {
    setCreating(false)
    setEditing(null)
  }

  function setField(key: keyof Omit<WeaponLibraryEntry, 'id'>, val: string | number) {
    setForm(f => ({ ...f, [key]: val }))
  }

  async function save() {
    if (!form.name.trim()) return
    setSaving(true)
    setError(null)
    try {
      if (editing) {
        const res = await weaponLibraryApi.update(editing.id, form)
        setWeapons(ws => ws.map(w => w.id === editing.id ? res.data : w))
      } else {
        const res = await weaponLibraryApi.create(form)
        setWeapons(ws => [...ws, res.data])
      }
      cancel()
    } catch {
      setError('Failed to save weapon')
    } finally {
      setSaving(false)
    }
  }

  async function remove(id: number) {
    if (!confirm('Delete this weapon?')) return
    try {
      await weaponLibraryApi.delete(id)
      setWeapons(ws => ws.filter(w => w.id !== id))
    } catch {
      setError('Failed to delete weapon')
    }
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h1 className="font-display text-2xl text-gold-500 tracking-widest uppercase">Weapon Library</h1>
        <button
          onClick={startCreate}
          className="px-4 py-2 bg-gold-600 hover:bg-gold-500 text-dark-900 font-semibold text-sm rounded transition-colors"
        >
          + New Weapon
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
            {editing ? 'Edit Weapon' : 'New Weapon'}
          </h3>

          {/* Row 1: Faction, Name, Cost, Sort Order */}
          <div className="grid grid-cols-4 gap-3">
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
              <label className="text-xs text-dark-300 block mb-1">Name</label>
              <input
                value={form.name}
                onChange={e => setField('name', e.target.value)}
                placeholder="e.g. Autopistol"
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

          {/* Row 2: Profile stats */}
          <div>
            <div className="text-xs text-dark-400 mb-1">Profile</div>
            <div className="grid grid-cols-8 gap-1">
              {PROFILE_LABELS.map(l => (
                <div key={l} className="text-center text-xs text-gold-600 font-display">{l}</div>
              ))}
              {PROFILE_KEYS.map(key => (
                <input
                  key={key}
                  type="text"
                  value={form[key as ProfileKey]}
                  onChange={e => setField(key, e.target.value)}
                  className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-1 py-1 text-xs text-center focus:outline-none focus:border-gold-600 font-mono"
                />
              ))}
            </div>
          </div>

          {/* Traits */}
          <div>
            <label className="text-xs text-dark-300 block mb-1">Traits</label>
            <input
              value={form.traits}
              onChange={e => setField('traits', e.target.value)}
              placeholder="e.g. Pistol, Rapid Fire (1)"
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
              className="px-4 py-1.5 text-sm bg-gold-600 hover:bg-gold-500 text-dark-900 font-semibold rounded transition-colors disabled:opacity-50"
            >
              {saving ? 'Saving…' : 'Save Weapon'}
            </button>
          </div>
        </div>
      )}

      {filtered.length === 0 ? (
        <div className="text-dark-400 text-sm text-center py-16 border border-dark-700 rounded">
          No weapons for {filterGang} yet. Click "+ New Weapon" to add one.
        </div>
      ) : (
        <div className="space-y-2">
          {filtered.map(w => (
            <div key={w.id} className="border border-dark-600 bg-dark-800 rounded p-4">
              <div className="flex items-start justify-between mb-2">
                <div>
                  <span className="font-display text-gold-400">{w.name}</span>
                  <span className="ml-3 text-xs font-mono text-dark-300">💰 {w.cost} credits</span>
                </div>
                <div className="flex gap-2">
                  <button
                    onClick={() => startEdit(w)}
                    className="text-xs text-gold-600 hover:text-gold-400 transition-colors"
                  >
                    Edit
                  </button>
                  <button
                    onClick={() => remove(w.id)}
                    className="text-xs text-red-600 hover:text-red-400 transition-colors"
                  >
                    Delete
                  </button>
                </div>
              </div>
              <div className="grid grid-cols-8 gap-1">
                {PROFILE_LABELS.map(l => (
                  <div key={l} className="text-center text-xs text-gold-700 font-display">{l}</div>
                ))}
                {PROFILE_KEYS.map(key => (
                  <div key={key} className="text-center text-xs font-mono text-dark-200">{w[key as ProfileKey]}</div>
                ))}
              </div>
              {w.traits && (
                <div className="mt-1 text-xs text-dark-400">
                  <span className="text-dark-500">Traits: </span>{w.traits}
                </div>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  )
}
