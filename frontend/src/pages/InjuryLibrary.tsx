import { useEffect, useState } from 'react'
import { injuryLibraryApi } from '../api'
import type { InjuryLibraryEntry } from '../types'

const EMPTY: Omit<InjuryLibraryEntry, 'id'> = {
  name: '', category: '', description: '', sort_order: 0,
}

export default function InjuryLibrary() {
  const [injuries, setInjuries] = useState<InjuryLibraryEntry[]>([])
  const [filterCategory, setFilterCategory] = useState<string>('')
  const [search, setSearch] = useState<string>('')
  const [editing, setEditing] = useState<InjuryLibraryEntry | null>(null)
  const [creating, setCreating] = useState(false)
  const [form, setForm] = useState<Omit<InjuryLibraryEntry, 'id'>>(EMPTY)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => { load() }, [])

  async function load() {
    setError(null)
    try {
      const res = await injuryLibraryApi.list()
      setInjuries(Array.isArray(res.data) ? res.data : [])
    } catch {
      setError('Failed to load injury library')
    }
  }

  const categories = Array.from(new Set(injuries.map(i => i.category).filter(Boolean))).sort()

  const filtered = injuries.filter(i =>
    (!filterCategory || i.category === filterCategory) &&
    (!search || i.name.toLowerCase().includes(search.toLowerCase()))
  )

  const grouped = filtered.reduce<Record<string, InjuryLibraryEntry[]>>((acc, i) => {
    const cat = i.category || 'Other'
    if (!acc[cat]) acc[cat] = []
    acc[cat].push(i)
    return acc
  }, {})

  function startCreate() {
    setForm({ ...EMPTY })
    setEditing(null)
    setCreating(true)
  }

  function startEdit(i: InjuryLibraryEntry) {
    setForm({ ...i })
    setEditing(i)
    setCreating(false)
  }

  function cancel() {
    setCreating(false)
    setEditing(null)
  }

  function setField(key: keyof Omit<InjuryLibraryEntry, 'id'>, val: string | number) {
    setForm(f => ({ ...f, [key]: val }))
  }

  async function save() {
    if (!form.name.trim()) return
    setSaving(true)
    setError(null)
    try {
      if (editing) {
        const res = await injuryLibraryApi.update(editing.id, form)
        setInjuries(is => is.map(i => i.id === editing.id ? res.data : i))
      } else {
        const res = await injuryLibraryApi.create(form)
        setInjuries(is => [...is, res.data])
      }
      cancel()
      load()
    } catch {
      setError('Failed to save injury')
    } finally {
      setSaving(false)
    }
  }

  async function remove(id: number) {
    if (!confirm('Delete this injury?')) return
    try {
      await injuryLibraryApi.delete(id)
      load()
    } catch {
      setError('Failed to delete injury')
    }
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h1 className="font-display text-2xl text-gold-500 tracking-widest uppercase">Injury Library</h1>
        <button
          onClick={startCreate}
          className="px-4 py-2 bg-gold-600 hover:bg-gold-500 text-dark-900 font-semibold text-sm rounded transition-colors"
        >
          + New Injury
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
            {editing ? 'Edit Injury' : 'New Injury'}
          </h3>

          <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div>
              <label className="text-xs text-dark-300 block mb-1">Name</label>
              <input
                value={form.name}
                onChange={e => setField('name', e.target.value)}
                placeholder="e.g. Chest Wound"
                className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
              />
            </div>
            <div>
              <label className="text-xs text-dark-300 block mb-1">Category</label>
              <input
                value={form.category}
                onChange={e => setField('category', e.target.value)}
                placeholder="e.g. Lasting Injury"
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
            <label className="text-xs text-dark-300 block mb-1">Description</label>
            <input
              value={form.description}
              onChange={e => setField('description', e.target.value)}
              placeholder="Short description of the injury effect"
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
              {saving ? 'Saving…' : 'Save'}
            </button>
          </div>
        </div>
      )}

      {filtered.length === 0 ? (
        <div className="text-dark-400 text-sm text-center py-16 border border-dark-700 rounded">
          No injuries found. Click "+ New Injury" to add one.
        </div>
      ) : (
        <div className="space-y-6">
          {Object.entries(grouped).sort(([a], [b]) => a.localeCompare(b)).map(([category, items]) => (
            <div key={category}>
              <h2 className="font-display text-xs text-gold-600 uppercase tracking-widest mb-2">{category}</h2>
              <div className="space-y-1">
                {items.map(i => (
                  <div key={i.id} className="border border-dark-600 bg-dark-800 rounded p-3">
                    <div className="flex items-start justify-between">
                      <div>
                        <span className="font-display text-gold-400 text-sm">{i.name}</span>
                        {i.description && (
                          <div className="mt-1 text-xs text-dark-400">{i.description}</div>
                        )}
                      </div>
                      <div className="flex gap-3 ml-4 shrink-0">
                        <button onClick={() => startEdit(i)} className="text-xs text-gold-600 hover:text-gold-400 transition-colors">Edit</button>
                        <button onClick={() => remove(i.id)} className="text-xs text-red-600 hover:text-red-400 transition-colors">Delete</button>
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
