import { create } from 'zustand'
import type { Gang, Fighter } from './types'
import { gangsApi, gangApi, fightersApi, fighterApi } from './api'

interface Store {
  gangs: Gang[]
  currentGang: Gang | null
  currentFighter: Fighter | null
  loading: boolean
  error: string | null

  fetchGangs: () => Promise<void>
  fetchGang: (id: number) => Promise<void>
  createGang: (data: Partial<Gang>) => Promise<Gang>
  updateGang: (id: number, data: Partial<Gang>) => Promise<void>
  deleteGang: (id: number) => Promise<void>

  fetchFighter: (id: number) => Promise<void>
  createFighter: (gangId: number, data: Partial<Fighter>) => Promise<Fighter>
  updateFighter: (id: number, data: Partial<Fighter>) => Promise<void>
  deleteFighter: (id: number) => Promise<void>

  clearError: () => void
}

export const useStore = create<Store>((set, get) => ({
  gangs:         [],
  currentGang:   null,
  currentFighter: null,
  loading:       false,
  error:         null,

  fetchGangs: async () => {
    set({ loading: true, error: null })
    try {
      const res = await gangsApi.list()
      set({ gangs: Array.isArray(res.data) ? res.data : [], loading: false })
    } catch {
      set({ error: 'Failed to load gangs', loading: false })
    }
  },

  fetchGang: async (id) => {
    set({ loading: true, error: null })
    try {
      const res = await gangApi.get(id)
      set({ currentGang: res.data, loading: false })
    } catch {
      set({ error: 'Failed to load gang', loading: false })
    }
  },

  createGang: async (data) => {
    const res = await gangsApi.create(data)
    set(s => ({ gangs: [...s.gangs, res.data] }))
    return res.data
  },

  updateGang: async (id, data) => {
    const res = await gangApi.update(id, data)
    set(s => ({
      gangs: s.gangs.map(g => g.id === id ? res.data : g),
      currentGang: s.currentGang?.id === id ? res.data : s.currentGang,
    }))
  },

  deleteGang: async (id) => {
    await gangApi.delete(id)
    set(s => ({ gangs: s.gangs.filter(g => g.id !== id) }))
  },

  fetchFighter: async (id) => {
    set({ loading: true, error: null })
    try {
      const res = await fighterApi.get(id)
      set({ currentFighter: res.data, loading: false })
    } catch {
      set({ error: 'Failed to load fighter', loading: false })
    }
  },

  createFighter: async (gangId, data) => {
    const res = await fightersApi.create(gangId, data)
    const gang = get().currentGang
    if (gang && gang.id === gangId) {
      set({
        currentGang: {
          ...gang,
          credits: gang.credits - (res.data.cost ?? 0),
          fighters: [...(gang.fighters ?? []), res.data],
        },
      })
    }
    return res.data
  },

  updateFighter: async (id, data) => {
    const res = await fighterApi.update(id, data)
    set(s => ({
      currentFighter: s.currentFighter?.id === id ? res.data : s.currentFighter,
      currentGang: s.currentGang
        ? {
            ...s.currentGang,
            fighters: s.currentGang.fighters?.map(f => f.id === id ? res.data : f),
          }
        : null,
    }))
  },

  deleteFighter: async (id) => {
    await fighterApi.delete(id)
    set(s => ({
      currentGang: s.currentGang
        ? { ...s.currentGang, fighters: s.currentGang.fighters?.filter(f => f.id !== id) }
        : null,
    }))
  },

  clearError: () => set({ error: null }),
}))
