import { useEffect, useState } from 'react'
import { skillLibraryApi } from '../api'
import type { SkillLibraryEntry } from '../types'

const GANG_TYPES: string[] = [
  'Universal',
  'Goliath', 'Escher', 'Van Saar', 'Delaque', 'Cawdor', 'Orlock',
  'House of Iron', 'Corpse Grinder Cult', 'Genestealer Cult', 'Enforcers',
]

const SKILL_CATEGORIES: string[] = [
  'Agility', 'Brawn', 'Combat', 'Cunning', 'Driving',
  'Ferocity', 'Leadership', 'Savant', 'Shooting', 'Telepathy', 'Telekinesis',
]

const ROLES: string[] = ['Leader', 'Champion', 'Ganger', 'Juve', 'Specialist', 'Prospect']

const EMPTY: Omit<SkillLibraryEntry, 'id'> = {
  name: '', category: '', factions: '', roles: '', sort_order: 0,
}

export default function SkillLibrary() {
  const [skills, setSkills] = useState<SkillLibraryEntry[]>([])
  const [filterCategory, setFilterCategory] = useState<string>('')
  const [search, setSearch] = useState<string>('')
  const [editing, setEditing] = useState<SkillLibraryEntry | null>(null)
  const [creating, setCreating] = useState(false)
  const [form, setForm] = useState<Omit<SkillLibraryEntry, 'id'>>(EMPTY)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => { load() }, [])

  async function load() {
    setError(null)
    try {
      const res = await skillLibraryApi.list()
      setSkills(Array.isArray(res.data) ? res.data : [])
    } catch {
      setError('Failed to load skill library')
    }
  }

  const categories = Array.from(new Set(skills.map(s => s.category).filter(Boolean))).sort()

  const filtered = skills.filter(s =>
    (!filterCategory || s.category === filterCategory) &&
    (!search || s.name.toLowerCase().includes(search.toLowerCase()))
  )

  const grouped = filtered.reduce<Record<string, SkillLibraryEntry[]>>((acc, s) => {
    const cat = s.category || 'Other'
    if (!acc[cat]) acc[cat] = []
    acc[cat].push(s)
    return acc
  }, {})

  function startCreate() {
    setForm({ ...EMPTY })
    setEditing(null)
    setCreating(true)
  }

  function startEdit(s: SkillLibraryEntry) {
    setForm({ ...s })
    setEditing(s)
    setCreating(false)
  }

  function cancel() {
    setCreating(false)
    setEditing(null)
  }

  function setField(key: keyof Omit<SkillLibraryEntry, 'id'>, val: string | number) {
    setForm(f => ({ ...f, [key]: val }))
  }

  async function save() {
    if (!form.name.trim()) return
    setSaving(true)
    setError(null)
    try {
      if (editing) {
        const res = await skillLibraryApi.update(editing.id, form)
        setSkills(ss => ss.map(s => s.id === editing.id ? res.data : s))
      } else {
        const res = await skillLibraryApi.create(form)
        setSkills(ss => [...ss, res.data])
      }
      cancel()
      load()
    } catch {
      setError('Failed to save skill')
    } finally {
      setSaving(false)
    }
  }

  async function remove(id: number) {
    if (!confirm('Delete this skill?')) return
    try {
      await skillLibraryApi.delete(id)
      load()
    } catch {
      setError('Failed to delete skill')
    }
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h1 className="font-display text-2xl text-gold-500 tracking-widest uppercase">Skill Library</h1>
        <button
          onClick={startCreate}
          className="px-4 py-2 bg-gold-600 hover:bg-gold-500 text-black font-semibold text-sm rounded transition-colors"
        >
          + New Skill
        </button>
      </div>

      <div className="flex flex-wrap gap-3 mb-6">
        <div>
          <label className="text-xs text-dark-300 block mb-1">Category</label>
          <select
            value={filterCategory}
            onChange={e => setFilterCategory(e.target.value)}
            className="bg-dark-800 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
          >
            <option value="">All categories</option>
            {categories.map(c => <option key={c} value={c}>{c}</option>)}
          </select>
        </div>
        <div>
          <label className="text-xs text-dark-300 block mb-1">Search</label>
          <input
            value={search}
            onChange={e => setSearch(e.target.value)}
            placeholder="Search by name…"
            className="bg-dark-800 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600 w-48"
          />
        </div>
      </div>

      {error && <div className="text-red-400 text-sm mb-4">{error}</div>}

      {(creating || editing) && (
        <div className="border border-gold-800 bg-dark-800 rounded p-4 mb-6 space-y-4">
          <h3 className="font-display text-gold-500 text-sm tracking-wider uppercase">
            {editing ? 'Edit Skill' : 'New Skill'}
          </h3>

          <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div>
              <label className="text-xs text-dark-300 block mb-1">Name</label>
              <input
                value={form.name}
                onChange={e => setField('name', e.target.value)}
                placeholder="e.g. Sprint"
                className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
              />
            </div>
            <div>
              <label className="text-xs text-dark-300 block mb-1">Category</label>
              <input
                value={form.category}
                onChange={e => setField('category', e.target.value)}
                placeholder="e.g. Agility"
                list="skill-categories"
                className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
              />
              <datalist id="skill-categories">
                {SKILL_CATEGORIES.map(c => <option key={c} value={c} />)}
              </datalist>
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
            <label className="text-xs text-dark-300 block mb-2">Factions</label>
            <div className="flex flex-wrap gap-x-4 gap-y-1.5">
              {GANG_TYPES.filter(g => g !== 'Universal').map(g => {
                const factionList = (form.factions || '').split(',').map(s => s.trim()).filter(Boolean)
                const checked = factionList.includes(g)
                return (
                  <label key={g} className="flex items-center gap-1.5 text-xs text-dark-300 cursor-pointer">
                    <input
                      type="checkbox"
                      checked={checked}
                      onChange={() => {
                        const updated = checked ? factionList.filter(f => f !== g) : [...factionList, g]
                        setField('factions', updated.join(','))
                      }}
                      className="accent-gold-500"
                    />
                    {g}
                  </label>
                )
              })}
            </div>
          </div>

          <div>
            <label className="text-xs text-dark-300 block mb-2">Roles</label>
            <div className="flex flex-wrap gap-x-4 gap-y-1.5">
              {ROLES.map(r => {
                const roleList = (form.roles || '').split(',').map(s => s.trim()).filter(Boolean)
                const checked = roleList.includes(r)
                return (
                  <label key={r} className="flex items-center gap-1.5 text-xs text-dark-300 cursor-pointer">
                    <input
                      type="checkbox"
                      checked={checked}
                      onChange={() => {
                        const updated = checked ? roleList.filter(x => x !== r) : [...roleList, r]
                        setField('roles', updated.join(','))
                      }}
                      className="accent-gold-500"
                    />
                    {r}
                  </label>
                )
              })}
            </div>
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
              {saving ? 'Saving…' : 'Save'}
            </button>
          </div>
        </div>
      )}

      {filtered.length === 0 ? (
        <div className="text-dark-400 text-sm text-center py-16 border border-dark-700 rounded">
          No skills found. Click "+ New Skill" to add one.
        </div>
      ) : (
        <div className="space-y-6">
          {Object.entries(grouped).sort(([a], [b]) => a.localeCompare(b)).map(([category, items]) => (
            <div key={category}>
              <h2 className="font-display text-xs text-gold-600 uppercase tracking-widest mb-2">{category}</h2>
              <div className="space-y-1">
                {items.map(s => (
                  <div key={s.id} className="border border-dark-600 bg-dark-800 rounded p-3">
                    <div className="flex items-start justify-between">
                      <div>
                        <span className="font-display text-gold-400 text-sm">{s.name}</span>
                        {s.factions && (
                          <div className="mt-1 text-xs text-dark-400">
                            <span className="text-dark-500">Factions: </span>{s.factions}
                          </div>
                        )}
                        {s.roles && (
                          <div className="mt-0.5 text-xs text-dark-400">
                            <span className="text-dark-500">Roles: </span>{s.roles}
                          </div>
                        )}
                      </div>
                      <div className="flex gap-3 ml-4 shrink-0">
                        <button onClick={() => startEdit(s)} className="text-xs text-gold-600 hover:text-gold-400 transition-colors">Edit</button>
                        <button onClick={() => remove(s.id)} className="text-xs text-red-600 hover:text-red-400 transition-colors">Delete</button>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  )
}
