import { useEffect, useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useStore } from '../store'
import StatBlock from '../components/StatBlock'
import type { FighterStats, Skill, Injury, Equipment, EquipmentType, Weapon, Armour, Wargear, SpecialRule, WeaponLibraryEntry } from '../types'
import { fighterApi, gangApi, weaponLibraryApi } from '../api'

export default function FighterDetail() {
  const { id } = useParams<{ id: string }>()
  const fighterId = Number(id)
  const { currentFighter, loading, error, fetchFighter, updateFighter } = useStore()
  const [editing, setEditing] = useState(false)
  const [editState, setEditState] = useState<any>(null)

  // Sub-form states
  const [skillName, setSkillName]       = useState('')
  const [skillCat, setSkillCat]         = useState('')
  const [injuryName, setInjuryName]     = useState('')
  const [injuryPerm, setInjuryPerm]     = useState(false)
  const [equipName, setEquipName]       = useState('')
  const [equipType, setEquipType]       = useState<EquipmentType>('weapon')
  const [equipCost, setEquipCost]       = useState(0)
  const [weaponName, setWeaponName]     = useState('')
  const [weaponCost, setWeaponCost]     = useState(0)
  const [weaponNotes, setWeaponNotes]   = useState('')
  const [woundChecks, setWoundChecks]           = useState<boolean[]>([])
  const [fleshWoundChecks, setFleshWoundChecks] = useState<boolean[]>([])
  const [armourName, setArmourName]     = useState('')
  const [armourCost, setArmourCost]     = useState(0)
  const [armourNotes, setArmourNotes]   = useState('')
  const [wargearName, setWargearName]   = useState('')
  const [wargearCost, setWargearCost]   = useState(0)
  const [wargearNotes, setWargearNotes] = useState('')
  const [ruleName, setRuleName]         = useState('')
  const [ruleDesc, setRuleDesc]         = useState('')
  const [refresh, setRefresh]           = useState(0)
  const [gangType, setGangType]         = useState<string | null>(null)
  const [weaponLibrary, setWeaponLibrary] = useState<WeaponLibraryEntry[]>([])

  useEffect(() => { fetchFighter(fighterId) }, [fighterId, fetchFighter, refresh])

  useEffect(() => {
    if (currentFighter) {
      setWoundChecks(Array(currentFighter.w).fill(false))
      setFleshWoundChecks(Array(currentFighter.t).fill(false))
    }
  }, [currentFighter?.id])

  useEffect(() => {
    if (!currentFighter) return
    gangApi.get(currentFighter.gang_id).then(res => {
      const gt = res.data.type
      setGangType(gt)
      weaponLibraryApi.list({ faction: gt }).then(r => setWeaponLibrary(Array.isArray(r.data) ? r.data : []))
    }).catch(() => {})
  }, [currentFighter?.gang_id])

  if (loading && !currentFighter) return <div className="text-dark-400 font-mono animate-pulse">Loading fighter…</div>
  if (error)   return <div className="text-blood-500">{error}</div>
  if (!currentFighter) return null

  const f = currentFighter

  const startEdit = () => {
    setEditState({
      experience: f.experience,
      kills: f.kills,
      advancement_count: f.advancement_count,
      in_recovery: f.in_recovery,
      dead: f.dead,
      m: f.m, ws: f.ws, bs: f.bs, s: f.s, t: f.t,
      w: f.w, i: f.i, a: f.a, ld: f.ld, cl: f.cl, wil: f.wil, int: f.int,
    })
    setEditing(true)
  }

  const saveEdit = async () => {
    await updateFighter(f.id, editState)
    setEditing(false)
  }

  const handleStatChange = (key: keyof FighterStats, val: number) => {
    setEditState((s: any) => ({ ...s, [key]: val }))
  }

  const addSkill = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!skillName.trim()) return
    await fighterApi.addSkill(f.id, { skill_name: skillName.trim(), skill_category: skillCat.trim() })
    setSkillName(''); setSkillCat('')
    setRefresh(r => r + 1)
  }

  const removeSkill = async (skillId: number) => {
    await fighterApi.deleteSkill(f.id, skillId)
    setRefresh(r => r + 1)
  }

  const addInjury = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!injuryName.trim()) return
    await fighterApi.addInjury(f.id, { injury_name: injuryName.trim(), permanent: injuryPerm })
    setInjuryName(''); setInjuryPerm(false)
    setRefresh(r => r + 1)
  }

  const removeInjury = async (injuryId: number) => {
    await fighterApi.deleteInjury(f.id, injuryId)
    setRefresh(r => r + 1)
  }

  const addEquipment = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!equipName.trim()) return
    await fighterApi.addEquipment(f.id, { name: equipName.trim(), type: equipType, cost: equipCost, traits: [] })
    setEquipName(''); setEquipCost(0)
    setRefresh(r => r + 1)
  }

  const removeEquipment = async (equipId: number) => {
    await fighterApi.deleteEquipment(f.id, equipId)
    setRefresh(r => r + 1)
  }

  const addWeapon = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!weaponName.trim()) return
    await fighterApi.addWeapon(f.id, { name: weaponName.trim(), cost: weaponCost, notes: weaponNotes.trim() })
    setWeaponName(''); setWeaponCost(0); setWeaponNotes('')
    setRefresh(r => r + 1)
  }

  const removeWeapon = async (weaponId: number) => {
    await fighterApi.deleteWeapon(f.id, weaponId)
    setRefresh(r => r + 1)
  }

  const addArmour = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!armourName.trim()) return
    await fighterApi.addArmour(f.id, { name: armourName.trim(), cost: armourCost, notes: armourNotes.trim() })
    setArmourName(''); setArmourCost(0); setArmourNotes('')
    setRefresh(r => r + 1)
  }

  const removeArmour = async (armourId: number) => {
    await fighterApi.deleteArmour(f.id, armourId)
    setRefresh(r => r + 1)
  }

  const addWargear = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!wargearName.trim()) return
    await fighterApi.addWargear(f.id, { name: wargearName.trim(), cost: wargearCost, notes: wargearNotes.trim() })
    setWargearName(''); setWargearCost(0); setWargearNotes('')
    setRefresh(r => r + 1)
  }

  const removeWargear = async (wargearId: number) => {
    await fighterApi.deleteWargear(f.id, wargearId)
    setRefresh(r => r + 1)
  }

  const addSpecialRule = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!ruleName.trim()) return
    await fighterApi.addSpecialRule(f.id, { rule_name: ruleName.trim(), description: ruleDesc.trim() })
    setRuleName(''); setRuleDesc('')
    setRefresh(r => r + 1)
  }

  const removeSpecialRule = async (ruleId: number) => {
    await fighterApi.deleteSpecialRule(f.id, ruleId)
    setRefresh(r => r + 1)
  }

  const woundsUsed      = woundChecks.filter(Boolean).length
  const fleshWoundsUsed = fleshWoundChecks.filter(Boolean).length

  const baseStats: FighterStats = editing ? editState : {
    m: f.m, ws: f.ws, bs: f.bs, s: f.s, t: f.t,
    w: f.w, i: f.i, a: f.a, ld: f.ld, cl: f.cl, wil: f.wil, int: f.int,
  }

  const stats: FighterStats = {
    ...baseStats,
    w: Math.max(0, baseStats.w - woundsUsed),
    t: Math.max(0, baseStats.t - fleshWoundsUsed),
  }

  const redStats: (keyof FighterStats)[] = [
    ...(woundsUsed > 0 ? ['w' as const] : []),
    ...(fleshWoundsUsed > 0 ? ['t' as const] : []),
  ]

  function toggleWound(i: number) {
    setWoundChecks(prev => prev.map((v, idx) => idx === i ? !v : v))
  }

  function toggleFleshWound(i: number) {
    setFleshWoundChecks(prev => prev.map((v, idx) => idx === i ? !v : v))
  }

  return (
    <div className="max-w-3xl">
      {/* Breadcrumb */}
      <div className="flex gap-2 text-xs text-dark-400 mb-4">
        <Link to="/" className="hover:text-gold-500 transition-colors">Gangs</Link>
        <span>›</span>
        <Link to={`/gangs/${f.gang_id}`} className="hover:text-gold-500 transition-colors">Gang</Link>
        <span>›</span>
        <span className="text-dark-200">{f.name}</span>
      </div>

      <div className="flex items-start justify-between mb-6">
        <div>
          <h1 className="font-display text-2xl text-gold-500 tracking-widest uppercase">{f.name}</h1>
          <div className="text-sm text-dark-300 mt-0.5">{f.type}</div>
          <div className="flex gap-2 mt-2">
            {f.dead        && <span className="text-xs bg-blood-600 text-dark-100 px-2 py-0.5 rounded font-mono">DEAD</span>}
            {f.in_recovery && <span className="text-xs bg-dark-600 text-dark-300 px-2 py-0.5 rounded font-mono">IN RECOVERY</span>}
          </div>
        </div>
        <div className="flex gap-2">
          {editing ? (
            <>
              <button onClick={() => setEditing(false)} className="text-sm text-dark-400 hover:text-dark-100 px-3 py-1.5 border border-dark-700 rounded transition-colors">Cancel</button>
              <button onClick={saveEdit} className="text-sm bg-gold-600 hover:bg-gold-500 text-dark-900 font-bold px-4 py-1.5 rounded transition-colors">Save</button>
            </>
          ) : (
            <button onClick={startEdit} className="text-sm border border-gold-700 text-gold-500 hover:bg-gold-900/30 px-4 py-1.5 rounded transition-colors">Edit</button>
          )}
        </div>
      </div>

      {/* Quick stats */}
      <div className="grid grid-cols-4 gap-3 mb-6">
        {[
          { label: 'Cost', icon: '💰', key: 'cost' as const },
          { label: 'XP', icon: '⭐', key: 'experience' as const },
          { label: 'Kills', icon: '☠', key: 'kills' as const },
          { label: 'Advancements', icon: '↑', key: 'advancement_count' as const },
        ].map(({ label, icon, key }) => (
          <div key={key} className="border border-dark-600 bg-dark-800 rounded p-3">
            <div className="text-xs text-dark-400">{icon} {label}</div>
            {editing && key !== 'cost' ? (
              <input
                type="number"
                min={0}
                value={editState[key]}
                onChange={e => setEditState((s: any) => ({ ...s, [key]: Number(e.target.value) }))}
                className="w-full bg-transparent text-gold-400 font-mono text-xl focus:outline-none"
              />
            ) : (
              <div className="font-mono text-xl text-gold-400">{f[key]}</div>
            )}
          </div>
        ))}
      </div>

      {editing && (
        <div className="flex gap-4 mb-4 text-sm">
          <label className="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" checked={editState.in_recovery}
              onChange={e => setEditState((s: any) => ({ ...s, in_recovery: e.target.checked }))}
              className="accent-gold-500"
            />
            <span className="text-dark-300">In Recovery</span>
          </label>
          <label className="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" checked={editState.dead}
              onChange={e => setEditState((s: any) => ({ ...s, dead: e.target.checked }))}
              className="accent-blood-500"
            />
            <span className="text-dark-300">Dead</span>
          </label>
        </div>
      )}

      {/* Stat block */}
      <div className="mb-4">
        <h2 className="font-display text-xs text-gold-600 uppercase tracking-widest mb-2">Stats</h2>
        <StatBlock stats={stats} editable={editing} onChange={handleStatChange} redStats={redStats} />
      </div>

      {/* Battle tracker */}
      {!editing && (
        <div className="mb-6 border border-dark-700 bg-dark-900/50 rounded p-3">
          <div className="flex items-center gap-2 mb-3">
            <h2 className="font-display text-xs text-gold-600 uppercase tracking-widest">Battle Tracker</h2>
            <button
              onClick={() => { setWoundChecks(Array(f.w).fill(false)); setFleshWoundChecks(Array(f.t).fill(false)) }}
              className="text-xs text-dark-400 hover:text-dark-200 transition-colors ml-auto"
            >
              Reset
            </button>
          </div>
          <div className="space-y-3">
            <div>
              <div className="text-xs text-dark-400 mb-1.5">Wounds <span className="font-mono text-dark-500">({stats.w} remaining)</span></div>
              <div className="flex gap-2 flex-wrap">
                {Array.from({ length: f.w }).map((_, i) => {
                  const checked = woundChecks[i] ?? false
                  return (
                    <label key={i} className="flex items-center gap-1 cursor-pointer">
                      <input
                        type="checkbox"
                        checked={checked}
                        onChange={() => toggleWound(i)}
                        className="w-5 h-5 accent-red-600 cursor-pointer"
                      />
                      <span className={`text-xs font-mono ${checked ? 'text-red-500' : 'text-dark-400'}`}>{i + 1}</span>
                    </label>
                  )
                })}
              </div>
            </div>
            <div>
              <div className="text-xs text-dark-400 mb-1.5">Flesh Wounds <span className="font-mono text-dark-500">({stats.t} remaining)</span></div>
              <div className="flex gap-2 flex-wrap">
                {Array.from({ length: f.t }).map((_, i) => {
                  const checked = fleshWoundChecks[i] ?? false
                  return (
                    <label key={i} className="flex items-center gap-1 cursor-pointer">
                      <input
                        type="checkbox"
                        checked={checked}
                        onChange={() => toggleFleshWound(i)}
                        className="w-5 h-5 accent-red-600 cursor-pointer"
                      />
                      <span className={`text-xs font-mono ${checked ? 'text-red-500' : 'text-dark-400'}`}>{i + 1}</span>
                    </label>
                  )
                })}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Weapons */}
      <Section title="Weapons">
        {(f.weapons ?? []).length > 0 && (
          <div className="space-y-1 mb-3">
            {(f.weapons ?? []).map((wp: Weapon) => (
              <div key={wp.id} className="flex justify-between items-center text-sm border border-dark-700 bg-dark-800 rounded px-3 py-1.5">
                <span>
                  <span className="text-dark-100">{wp.name}</span>
                  {wp.cost > 0 && <span className="text-dark-400 text-xs ml-2">💰{wp.cost}</span>}
                  {wp.notes && <span className="text-dark-400 text-xs ml-2">{wp.notes}</span>}
                </span>
                <button onClick={() => removeWeapon(wp.id)} className="text-dark-500 hover:text-blood-500 transition-colors text-xs">✕</button>
              </div>
            ))}
          </div>
        )}
        {weaponLibrary.length > 0 && (
          <div className="mb-3">
            <select
              value=""
              onChange={e => {
                const w = weaponLibrary.find(w => String(w.id) === e.target.value)
                if (!w) return
                setWeaponName(w.name)
                setWeaponCost(w.cost)
                const notes = [
                  w.range_s !== '-' ? `Rng ${w.range_s}/${w.range_l}` : '',
                  w.str !== '-' ? `Str ${w.str}` : '',
                  w.ap !== '-' ? `AP ${w.ap}` : '',
                  w.dmg !== '-' ? `Dmg ${w.dmg}` : '',
                  w.ammo !== '-' ? `Ammo ${w.ammo}` : '',
                  w.traits || '',
                ].filter(Boolean).join(', ')
                setWeaponNotes(notes)
              }}
              className="w-full bg-dark-700 border border-dark-600 text-dark-100 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-gold-600"
            >
              <option value="">— Pick from {gangType} equipment list —</option>
              {Array.from(new Set(weaponLibrary.map(w => w.category))).map(cat => (
                <optgroup key={cat} label={cat}>
                  {weaponLibrary.filter(w => w.category === cat).map(w => (
                    <option key={w.id} value={w.id}>
                      {w.name}{w.cost > 0 ? ` (${w.cost} cr)` : ''}
                    </option>
                  ))}
                </optgroup>
              ))}
            </select>
          </div>
        )}
        <form onSubmit={addWeapon} className="flex gap-2">
          <input value={weaponName} onChange={e => setWeaponName(e.target.value)} placeholder="Weapon name" className="input-sm flex-1" />
          <input type="number" min={0} value={weaponCost} onChange={e => setWeaponCost(Number(e.target.value))} placeholder="Cost" className="input-sm w-20" />
          <input value={weaponNotes} onChange={e => setWeaponNotes(e.target.value)} placeholder="Notes" className="input-sm w-32" />
          <button type="submit" className="btn-sm">Add</button>
        </form>
      </Section>

      {/* Armour */}
      <Section title="Armour">
        {(f.armour ?? []).length > 0 && (
          <div className="space-y-1 mb-3">
            {(f.armour ?? []).map((ar: Armour) => (
              <div key={ar.id} className="flex justify-between items-center text-sm border border-dark-700 bg-dark-800 rounded px-3 py-1.5">
                <span>
                  <span className="text-dark-100">{ar.name}</span>
                  {ar.cost > 0 && <span className="text-dark-400 text-xs ml-2">💰{ar.cost}</span>}
                  {ar.notes && <span className="text-dark-400 text-xs ml-2">{ar.notes}</span>}
                </span>
                <button onClick={() => removeArmour(ar.id)} className="text-dark-500 hover:text-blood-500 transition-colors text-xs">✕</button>
              </div>
            ))}
          </div>
        )}
        <form onSubmit={addArmour} className="flex gap-2">
          <input value={armourName} onChange={e => setArmourName(e.target.value)} placeholder="Armour name" className="input-sm flex-1" />
          <input type="number" min={0} value={armourCost} onChange={e => setArmourCost(Number(e.target.value))} placeholder="Cost" className="input-sm w-20" />
          <input value={armourNotes} onChange={e => setArmourNotes(e.target.value)} placeholder="Notes" className="input-sm w-32" />
          <button type="submit" className="btn-sm">Add</button>
        </form>
      </Section>

      {/* Wargear */}
      <Section title="Wargear">
        {(f.wargear ?? []).length > 0 && (
          <div className="space-y-1 mb-3">
            {(f.wargear ?? []).map((wg: Wargear) => (
              <div key={wg.id} className="flex justify-between items-center text-sm border border-dark-700 bg-dark-800 rounded px-3 py-1.5">
                <span>
                  <span className="text-dark-100">{wg.name}</span>
                  {wg.cost > 0 && <span className="text-dark-400 text-xs ml-2">💰{wg.cost}</span>}
                  {wg.notes && <span className="text-dark-400 text-xs ml-2">{wg.notes}</span>}
                </span>
                <button onClick={() => removeWargear(wg.id)} className="text-dark-500 hover:text-blood-500 transition-colors text-xs">✕</button>
              </div>
            ))}
          </div>
        )}
        <form onSubmit={addWargear} className="flex gap-2">
          <input value={wargearName} onChange={e => setWargearName(e.target.value)} placeholder="Wargear name" className="input-sm flex-1" />
          <input type="number" min={0} value={wargearCost} onChange={e => setWargearCost(Number(e.target.value))} placeholder="Cost" className="input-sm w-20" />
          <input value={wargearNotes} onChange={e => setWargearNotes(e.target.value)} placeholder="Notes" className="input-sm w-32" />
          <button type="submit" className="btn-sm">Add</button>
        </form>
      </Section>

      {/* Equipment */}
      <Section title="Equipment">
        {(f.equipment ?? []).length > 0 && (
          <div className="space-y-1 mb-3">
            {(f.equipment ?? []).map((eq: Equipment) => (
              <div key={eq.id} className="flex justify-between items-center text-sm border border-dark-700 bg-dark-800 rounded px-3 py-1.5">
                <span>
                  <span className="text-dark-100">{eq.name}</span>
                  <span className="text-dark-400 text-xs ml-2">[{eq.type}]</span>
                  {eq.cost > 0 && <span className="text-dark-400 text-xs ml-2">💰{eq.cost}</span>}
                </span>
                <button onClick={() => removeEquipment(eq.id)} className="text-dark-500 hover:text-blood-500 transition-colors text-xs">✕</button>
              </div>
            ))}
          </div>
        )}
        <form onSubmit={addEquipment} className="flex gap-2">
          <input value={equipName} onChange={e => setEquipName(e.target.value)} placeholder="Item name" className="input-sm flex-1" />
          <select value={equipType} onChange={e => setEquipType(e.target.value as EquipmentType)} className="input-sm w-28">
            <option value="weapon">Weapon</option>
            <option value="armour">Armour</option>
            <option value="equipment">Equipment</option>
          </select>
          <input type="number" min={0} value={equipCost} onChange={e => setEquipCost(Number(e.target.value))} placeholder="Cost" className="input-sm w-20" />
          <button type="submit" className="btn-sm">Add</button>
        </form>
      </Section>

      {/* Skills */}
      <Section title="Skills">
        {(f.skills ?? []).length > 0 && (
          <div className="space-y-1 mb-3">
            {(f.skills ?? []).map((sk: Skill) => (
              <div key={sk.id} className="flex justify-between items-center text-sm border border-dark-700 bg-dark-800 rounded px-3 py-1.5">
                <span><span className="text-gold-500">{sk.skill_name}</span>{sk.skill_category && <span className="text-dark-400 ml-2 text-xs">({sk.skill_category})</span>}</span>
                <button onClick={() => removeSkill(sk.id)} className="text-dark-500 hover:text-blood-500 transition-colors text-xs">✕</button>
              </div>
            ))}
          </div>
        )}
        <form onSubmit={addSkill} className="flex gap-2">
          <input value={skillName} onChange={e => setSkillName(e.target.value)} placeholder="Skill name" className="input-sm flex-1" />
          <input value={skillCat} onChange={e => setSkillCat(e.target.value)} placeholder="Category" className="input-sm w-32" />
          <button type="submit" className="btn-sm">Add</button>
        </form>
      </Section>

      {/* Special Rules */}
      <Section title="Special Rules">
        {(f.special_rules ?? []).length > 0 && (
          <div className="space-y-1 mb-3">
            {(f.special_rules ?? []).map((sr: SpecialRule) => (
              <div key={sr.id} className="flex justify-between items-center text-sm border border-dark-700 bg-dark-800 rounded px-3 py-1.5">
                <span>
                  <span className="text-gold-500">{sr.rule_name}</span>
                  {sr.description && <span className="text-dark-400 ml-2 text-xs">{sr.description}</span>}
                </span>
                <button onClick={() => removeSpecialRule(sr.id)} className="text-dark-500 hover:text-blood-500 transition-colors text-xs">✕</button>
              </div>
            ))}
          </div>
        )}
        <form onSubmit={addSpecialRule} className="flex gap-2">
          <input value={ruleName} onChange={e => setRuleName(e.target.value)} placeholder="Rule name" className="input-sm flex-1" />
          <input value={ruleDesc} onChange={e => setRuleDesc(e.target.value)} placeholder="Description" className="input-sm flex-1" />
          <button type="submit" className="btn-sm">Add</button>
        </form>
      </Section>

      {/* Injuries */}
      <Section title="Injuries">
        {(f.injuries ?? []).length > 0 && (
          <div className="space-y-1 mb-3">
            {(f.injuries ?? []).map((inj: Injury) => (
              <div key={inj.id} className="flex justify-between items-center text-sm border border-dark-700 bg-dark-800 rounded px-3 py-1.5">
                <span>
                  <span className={inj.permanent ? 'text-blood-500' : 'text-dark-200'}>{inj.injury_name}</span>
                  {inj.permanent && <span className="text-xs text-blood-600 ml-2">permanent</span>}
                </span>
                <button onClick={() => removeInjury(inj.id)} className="text-dark-500 hover:text-blood-500 transition-colors text-xs">✕</button>
              </div>
            ))}
          </div>
        )}
        <form onSubmit={addInjury} className="flex gap-2 items-center">
          <input value={injuryName} onChange={e => setInjuryName(e.target.value)} placeholder="Injury name" className="input-sm flex-1" />
          <label className="flex items-center gap-1.5 text-xs text-dark-300 cursor-pointer whitespace-nowrap">
            <input type="checkbox" checked={injuryPerm} onChange={e => setInjuryPerm(e.target.checked)} className="accent-blood-500" />
            Permanent
          </label>
          <button type="submit" className="btn-sm">Add</button>
        </form>
      </Section>
    </div>
  )
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <div className="mb-6">
      <h2 className="font-display text-xs text-gold-600 uppercase tracking-widest mb-2">{title}</h2>
      <div className="border border-dark-700 bg-dark-900/50 rounded p-3">{children}</div>
    </div>
  )
}
